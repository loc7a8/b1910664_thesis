
-- Categories

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (id, name, description) VALUES
(1, 'Espresso', 'Strong and concentrated coffee'),
(2, 'Cappuccino', 'Espresso with steamed milk and froth'),
(3, 'Latte', 'Espresso with steamed milk'),
(4, 'Mocha', 'Espresso with chocolate and steamed milk'),
(5, 'Iced Coffee', 'Chilled coffee served over ice');


-- Products

CREATE TABLE `products` (
  `id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `price` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Unavailable,1=Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



INSERT INTO products (id, category_id, name, description, price, status)
VALUES
    -- Products for category with id 1
    (1, 1, 'Espresso Shot', 'Single shot of espresso', 2.99, 1),
    (2, 1, 'Double Espresso', 'Double shot of espresso', 3.99, 1),
    (3, 1, 'Espresso Macchiato', 'Espresso with a dollop of milk foam', 3.49, 1),

    -- Products for category with id 2
    (4, 2, 'Classic Cappuccino', 'Equal parts espresso, steamed milk, and froth', 4.99, 1),
    (5, 2, 'Caramel Cappuccino', 'Cappuccino with caramel syrup', 5.49, 1),
    (6, 2, 'Hazelnut Cappuccino', 'Cappuccino with hazelnut flavor', 5.49, 1),

    -- Products for category with id 3
    (7, 3, 'Vanilla Latte', 'Latte with vanilla syrup', 4.99, 1),
    (8, 3, 'Caramel Latte', 'Latte with caramel syrup', 5.49, 1),
    (9, 3, 'Hazelnut Latte', 'Latte with hazelnut flavor', 5.49, 1),

    -- Products for category with id 4
    (10, 4, 'Mocha Frappuccino', 'Iced blended drink with espresso and chocolate', 5.99, 1),
    (11, 4, 'White Chocolate Mocha', 'Mocha with white chocolate syrup', 5.49, 1),
    (12, 4, 'Peppermint Mocha', 'Mocha with peppermint flavor', 5.49, 1),

    -- Products for category with id 5
    (13, 5, 'Iced Americano', 'Chilled espresso with water', 3.99, 1),
    (14, 5, 'Iced Caramel Macchiato', 'Iced drink with espresso, milk, and caramel', 4.99, 1),
    (15, 5, 'Iced Vanilla Latte', 'Iced latte with vanilla syrup', 4.99, 1);
	
	
	
-- Orders List

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `total_amount` float NOT NULL,
  `amount_tendered` float NOT NULL,
  `order_number` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Order Items

CREATE TABLE `order_items` (
  `id` int(30) NOT NULL,
  `order_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `qty` int(30) NOT NULL,
  `price` float NOT NULL,
  `amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Users List

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 3 COMMENT '1=Admin,2=Staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
-- Username: admin; Password: admin123
(1, 'admin', 'admin', '0192023a7bbd73250516f069df18b500', 1),
-- Username: staff; Password: staff123
(2, 'staff', 'staff', 'de9bf5643eabf80f4a56fda3bbb84483', 2);


--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);


--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);
  
-- Message

 CREATE TABLE `message` (
   `id` int,
   `from` int,
   `to` int,
   `reply` int,   
   `content` text,
	 `time` datetime,
	`seen` int
);