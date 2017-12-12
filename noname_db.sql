
--
-- Contenu de la table `delivery_type`
--

INSERT IGNORE INTO `delivery_type` (`id`, `name`, `delay`, `price`) VALUES
(1, 'Vol oiseau', 5, 10);

--
-- Contenu de la table `product_type`
--

INSERT IGNORE INTO `product_type` (`id`, `name`) VALUES
(1, 'Consommables');

--
-- Contenu de la table `product`
--

INSERT IGNORE INTO `product` (`id`, `product_type_id`, `name`, `price`, `description`, `photo_file`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 1, 'Chaussette', 5.000000000, 'ce sont juste des chaussettes bro !', '1.jpg', '2017-11-06 20:56:31', '2017-11-06 20:56:31', 'sowbiba', NULL);

--
-- Contenu de la table `role`
--

INSERT IGNORE INTO `role` (`id`, `name`) VALUES
(2, 'ADMIN'),
(1, 'BACK'),
(3, 'MEMBER');

--
-- Contenu de la table `stock`
--

INSERT IGNORE INTO `stock` (`id`, `product_id`, `quantity`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 1, 20, '2017-11-06 20:56:31', '2017-11-06 20:57:56', 'sowbiba', 'sowbiba');

--
-- Contenu de la table `user`
--

INSERT IGNORE INTO `user` (`id`, `firstname`, `lastname`, `phone`, `address`, `birthdate`, `email`, `username`, `password`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'Ibrahima', 'SOW', '0102030405', '202 Avenue de Général LECLERC', '1985-05-06 00:00:00', 'sowbiba@hotmail.com', 'sowbiba', 'ed7b9b3734926d3533f1fa3733338a317fa36e7d', '2017-10-29 13:43:40', '2017-10-29 13:43:40', 1);