<?php

namespace AppBundle\ParamConverter;

use AppBundle\Exception\EntityNotFilterableException;
use AppBundle\Exception\EntityNotSortableException;
use AppBundle\Model\Collection;
use AppBundle\Utils\Implementer;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

/**
 * Converts request parameters to a collection of objects (ordered and filtered) and stores
 * them as request attributes, so they can be injected as controller method arguments.
 */
class CollectionParamConverter implements ParamConverterInterface
{
    const DOCTRINE_JOIN = 'join';
    const DOCTRINE_INNER_JOIN = 'innerJoin';
    const DOCTRINE_LEFT_JOIN = 'leftJoin';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws \BadMethodCallException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (!isset($options['name'])) {
            throw new \BadMethodCallException('The "name" option is required.');
        } elseif (!is_string($options['name']) || '' === $options['name']) {
            throw new \BadMethodCallException('The "name" option must be a not empty string.');
        }

        $queries = $request->query->all();

        $orderBy = null;
        if (isset($queries['orderBy'])) {
            $orderBy = [];

            foreach (explode(',', $queries['orderBy']) as $order) {
                $order = explode(' ', trim($order));

                if (!isset($order[1])) {
                    throw new \BadMethodCallException(
                        'Wrong orderBy parameter format (COLUMN_NAME ORDER[, COLUMN_NAME ORDER ...])'
                    );
                }

                $orderBy[$order[0]] = $order[1];
            }
        }

        unset($queries['fields']);
        unset($queries['limit']);
        unset($queries['orderBy']);
        unset($queries['page']);

        $routeParams = $request->attributes->get('_route_params');
        unset($routeParams['_format']);

        $criteria = array_merge($queries, $routeParams);

        $request->attributes->set(
            $configuration->getName(),
            new Collection($options['name'], $this->findBy($configuration, $criteria, $orderBy))
        );

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        if ('collection_param_converter' !== $configuration->getConverter()) {
            return false;
        }

        return true;
    }

    /**
     * @param ParamConverter $configuration
     * @param array          $criteria
     * @param array|null     $orderBy
     *
     * @return array
     *
     * @throws EntityNotFilterableException
     * @throws EntityNotSortableException
     */
    private function findBy(ParamConverter $configuration, array $criteria, array $orderBy = null)
    {
        $repository = $this->entityManager->getRepository($configuration->getClass());
        $entityName = $repository->getClassName();

        if (empty($criteria) && empty($orderBy)) {
            return $repository->findBy($criteria, $orderBy);
        }

        // todo: Use FilterableInterface::class when the PHP version is >= 5.5
        $isFilterable = Implementer::implementsInterface($entityName, 'AppBundle\Model\FilterableInterface');
        // todo: Use SortableInterface::class when the PHP version is >= 5.5
        $isSortable = Implementer::implementsInterface($entityName, 'AppBundle\Model\SortableInterface');

        if (!empty($criteria) && !$isFilterable) {
            throw new EntityNotFilterableException('This resource is not filterable!');
        }

        if (!empty($orderBy) && !$isSortable) {
            throw new EntityNotSortableException('This resource is not sortable!');
        }

        if (empty($orderBy) && $isSortable) {
            $orderBy = $entityName::getDefaultSortOrder();
        }

        $filtersMapping = $isFilterable ? $entityName::getFiltersMapping() : [];
        $ordersMapping = $isSortable ? $entityName::getOrdersMapping() : [];

        foreach (array_keys($criteria) as $fieldName) {
            if (!isset($filtersMapping[$fieldName])) {
                throw new \InvalidArgumentException(sprintf('Unknown filter %s.', $fieldName));
            }
        }

        foreach (array_keys((array) $orderBy) as $fieldName) {
            if (!isset($ordersMapping[$fieldName])) {
                throw new \InvalidArgumentException(sprintf('Unknown order %s.', $fieldName));
            }
        }

        $mapping = array_merge($ordersMapping, $filtersMapping);

        $joins = [];
        $qb = $repository->createQueryBuilder('self');

        foreach ($mapping as $filterKey => $filter) {
            if ((isset($criteria[$filterKey]) || isset($orderBy[$filterKey])) && false !== strpos($filter['field'], '.')) {
                $alias = null;
                for ($i = 0; $i < count($relations = explode('.', 'self.'.$filter['field'])) - 2; ++$i) {
                    $join = (null === $alias ? $relations[$i] : $alias).".{$relations[$i + 1]}";

                    $alias = str_replace('.', '_', $join);

                    if (!in_array($join, $joins)) {
                        $joinMethod = isset($filter['join']) ? $filter['join'] : static::DOCTRINE_JOIN;
                        $qb->$joinMethod($join, $alias);
                        $joins[] = $join;
                    }
                }
            }

            if (isset($criteria[$filterKey])) {
                if (1 < count($relations = explode('.', $filter['field']))) {
                    $field = array_pop($relations);
                    $where = 'self_'.implode('_', $relations).".$field";
                } else {
                    $where = 'self.'.$filter['field'];
                }

                if (false !== strpos($criteria[$filterKey], ',')) {
                    $qb->andWhere("$where IN(:$filterKey)");
                } elseif (false === strpos($criteria[$filterKey], '%') && false === strpos($criteria[$filterKey], '_')) {
                    $qb->andWhere("$where = :$filterKey");
                } else {
                    $qb->andWhere("$where LIKE :$filterKey");
                }

                if (false !== strpos($criteria[$filterKey], ',')) {
                    $qb->setParameter($filterKey, array_filter(array_map('trim', explode(',', $criteria[$filterKey]))));
                } else {
                    $qb->setParameter($filterKey, $criteria[$filterKey]);
                }
            }

            if (isset($orderBy[$filterKey])) {
                if (1 < count($relations = explode('.', $filter['field']))) {
                    $field = array_pop($relations);
                    $order = 'self_'.implode('_', $relations).".$field";
                } else {
                    $order = 'self.'.$filter['field'];
                }

                $qb->addOrderBy($order, $orderBy[$filterKey]);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
