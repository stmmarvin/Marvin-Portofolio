DROP DATABASE IF EXISTS MarvinPortofolio;
/*Make a new database*/
CREATE DATABASE IF NOT EXISTS  MarvinPortofolio;
USE MarvinPortofolio;

/*create tables*/

DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Projects;

CREATE TABLE IF NOT EXISTS User(
	Id        INT  		UNSIGNED  			   	 NOT NULL  		 auto_increment  
,   Username        	Varchar(50)              NOT NULL
,   Email           	Varchar(100)     UNIQUE  NOT NULL
,   Firstname           varchar(100)             NOT NULL
,   Lastname            Varchar(100)             NOT NULL
,   PasswordHash        varchar(255)             NOT NULL
,   PRIMARY KEY (Id)
) ENGINE = InnoDB charset utf8mb4;

INSERT INTO User
(Username, Email, Firstname, Lastname, PasswordHash)
VALUES
('marvin', 'marvinakpabot@gmail.com', 'Marvin', 'Akpabot', '$2y$10$2ObYfPCJ7ZjcONJw8gN.JO/CqeYxoPG3f7c6ahDi3Niq4eH9QmH7y');

CREATE TABLE IF NOT EXISTS Projects(
	Id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	Title VARCHAR(150) NOT NULL,
	Description TEXT NOT NULL,
	ProjectLink VARCHAR(255) DEFAULT NULL,
	ZipPath VARCHAR(255) DEFAULT NULL,
	ImagePath VARCHAR(255) DEFAULT NULL,
	CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (Id)
) ENGINE=InnoDB CHARSET=utf8mb4;


