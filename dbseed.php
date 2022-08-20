<?php
require 'bootstrap.php';


$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS users (
        id BIGINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        user_type INT DEFAULT 1,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL,
        
        PRIMARY KEY (id)
        
    ) ENGINE=INNODB;

    INSERT INTO users
        (name, email, user_type, password)
    VALUES
        ('bawan', 'bawanzana@gmail.com', 1, '$2y$10$.HR25wKEZ7jXsZeJQvrzmeUx5OaB4L/QU.XE/eIEkjqK7sjI8e1Zy');

    CREATE TABLE IF NOT EXISTS boards (
            id BIGINT NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            user_id BIGINT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL,

            PRIMARY KEY (id),

            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE SET NULL
            
        ) ENGINE=INNODB;
    
       
        
        CREATE TABLE IF NOT EXISTS statuses (
                id BIGINT NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(300)  DEFAULT NULL,
                user_id BIGINT DEFAULT NULL,
                board_id BIGINT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                PRIMARY KEY (id),

                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE SET NULL,

                FOREIGN KEY (board_id)
                REFERENCES boards(id)
                ON DELETE SET NULL
                
            ) ENGINE=INNODB;
        
            
        
        CREATE TABLE IF NOT EXISTS tasks (
                id BIGINT NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description VARCHAR(300)  DEFAULT NULL,
                due_date DATE,
                image VARCHAR(255) DEFAULT NULL,
                user_id BIGINT DEFAULT NULL,
                status_id BIGINT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                PRIMARY KEY (id),

                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE SET NULL,

                FOREIGN KEY (status_id)
                REFERENCES statuses(id)
                ON DELETE SET NULL
                
            ) ENGINE=INNODB;

        CREATE TABLE IF NOT EXISTS labels (
                id BIGINT NOT NULL AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                user_id BIGINT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                PRIMARY KEY (id),

                FOREIGN KEY (user_id)
                REFERENCES users(id)
                ON DELETE SET NULL
                
            ) ENGINE=INNODB;
        
        CREATE TABLE IF NOT EXISTS tasks_labels (
                id BIGINT NOT NULL AUTO_INCREMENT,
                task_id BIGINT DEFAULT NULL,
                label_id BIGINT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                PRIMARY KEY (id),

                FOREIGN KEY (task_id)
                REFERENCES tasks(id)
                ON DELETE SET NULL,

                FOREIGN KEY (label_id)
                REFERENCES labels(id)
                ON DELETE SET NULL
                
            ) ENGINE=INNODB;    

           
            
            CREATE TABLE IF NOT EXISTS logs (
                    id BIGINT NOT NULL AUTO_INCREMENT,
                    task_id BIGINT DEFAULT NULL,
                    task VARCHAR(100)  DEFAULT NULL,
                    user VARCHAR(100)  DEFAULT NULL,
                    action VARCHAR(300)  DEFAULT NULL,

                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    
                    PRIMARY KEY (id),

                    FOREIGN KEY (task_id)
                    REFERENCES tasks(id)
                    ON DELETE SET NULL
                        
                ) ENGINE=INNODB;
        
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}