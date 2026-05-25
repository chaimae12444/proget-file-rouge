CREATE DATABASE parapharmacie;
USE parapharmacie;

-- Table user
CREATE TABLE user (
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    password VARCHAR(255)
);

-- Table categorie
CREATE TABLE categorie (
    idCategorie INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100)
);

-- Table product
CREATE TABLE product (
    id_Product INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150),
    description TEXT,
    prix DECIMAL(10,2),
    idCategorie INT,
    
    FOREIGN KEY (idCategorie)
    REFERENCES categorie(idCategorie)
);

-- Table cart
CREATE TABLE cart (
    idCart INT PRIMARY KEY AUTO_INCREMENT,
    Qte INT,
    idUser INT,
    id_Product INT,
    
    FOREIGN KEY (idUser)
    REFERENCES user(idUser),
    
    FOREIGN KEY (id_Product)
    REFERENCES product(id_Product)
);

-- Table orders
CREATE TABLE orders (
    id_Orders INT PRIMARY KEY AUTO_INCREMENT,
    date_order DATE,
    Adresse VARCHAR(255),
    Telephone VARCHAR(20),
    idCart INT,
    
    FOREIGN KEY (idCart)
    REFERENCES cart(idCart)
);
