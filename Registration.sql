
CREATE DATABASE IF NOT EXISTS Registration;
USE Registration;

DROP TABLE IF EXISTS Registration;

CREATE TABLE Registration (
    idNum      VARCHAR(20)  PRIMARY KEY,           
    campus     VARCHAR(20)  NOT NULL,              
    studFName  VARCHAR(50)  NOT NULL,
    studLName  VARCHAR(50)  NOT NULL,
    amountPaid DECIMAL(10,2) NOT NULL DEFAULT 0,
    attended   VARCHAR(3)   NOT NULL DEFAULT 'No'  
);


