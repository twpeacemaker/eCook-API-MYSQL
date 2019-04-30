-- Purpose: to create for the eCook andriod application
-- By: Thomas Peacemaker
-- Contributors: Dan Smith and Ricky Oehler
use twpeacemaker_db;

-- will drop the tables before if that table has already been created
-- deletes them in the oppsite order they were created to aviod conflicts
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Theme;
DROP TABLE IF EXISTS RecipeUserMap;
DROP TABLE IF EXISTS Recipe;
DROP TABLE IF EXISTS RecipeOrgin;
DROP TABLE IF EXISTS RecipeIngredients;
DROP TABLE IF EXISTS Ingredients;
DROP TABLE IF EXISTS Units;
DROP TABLE IF EXISTS RecipeStep;
DROP TABLE IF EXISTS StepTimer;
SET FOREIGN_KEY_CHECKS=1;

-- creates the table to hold themes
CREATE TABLE Theme (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  theme_name nvarchar(10)
);

-- creates the table to hold User
CREATE TABLE User (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username NVARCHAR(100) NOT NULL UNIQUE,
  password NVARCHAR(100) NOT NULL,
  fkey_theme_id INT NOT NULL,
  FOREIGN KEY (fkey_theme_id) REFERENCES Theme(id) ON DELETE CASCADE
);

CREATE TABLE Recipe (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name NVARCHAR(200) NOT NULL,
  description NVARCHAR (20000) NOT NULL,
  fkey_creator_id INT NOT NULL,
  created_at DATETIME NOT NULL,
  viewable BOOLEAN NOT NULL DEFAULT 0,
  picture_path NVARCHAR(500) NOT NULL DEFAULT '',
  FOREIGN KEY (fkey_creator_id) REFERENCES User(id) ON DELETE CASCADE
);
--
CREATE TABLE RecipeUserMap (
  fkey_user_id INT NOT NULL,
  fkey_recipe_id INT NOT NULL,
  favorite BOOLEAN NOT NULL DEFAULT 0,
  FOREIGN KEY (fkey_user_id) REFERENCES User(id) ON DELETE CASCADE,
  FOREIGN KEY (fkey_recipe_id) REFERENCES Recipe(id) ON DELETE CASCADE,
  PRIMARY KEY(fkey_user_id, fkey_recipe_id)
);

CREATE TABLE RecipeOrgin (
  fkey_recipe_id INT NOT NULL,
  fkey_parent_id INT NOT NULL,
  FOREIGN KEY (fkey_recipe_id) REFERENCES Recipe(id) ON DELETE CASCADE,
  FOREIGN KEY (fkey_parent_id) REFERENCES Recipe(id) ON DELETE CASCADE
);

CREATE TABLE Ingredients (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name NVARCHAR(100)
);

CREATE TABLE Units (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name NVARCHAR(30),
  short_name NVARCHAR(10)
);

CREATE TABLE RecipeIngredients (
  fkey_recipe_id INT NOT NULL,
  fkey_ingredient_id INT NOT NULL,
  fkey_unit_id INT NOT NULL,
  amount INT NOT NULL,
  FOREIGN KEY (fkey_recipe_id) REFERENCES Recipe(id) ON DELETE CASCADE,
  FOREIGN KEY (fkey_ingredient_id) REFERENCES Ingredients(id) ON DELETE CASCADE,
  FOREIGN KEY (fkey_unit_id) REFERENCES Units(id) ON DELETE CASCADE
);


CREATE TABLE RecipeStep (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fkey_recipe_id INT NOT NULL,
  -- name NVARCHAR(500)  NOT NULL,
  description NVARCHAR(20000)  NOT NULL,
  FOREIGN KEY (fkey_recipe_id) REFERENCES Recipe(id) ON DELETE CASCADE
);

CREATE TABLE StepTimer (
  fkey_recipe_id INT NOT NULL AUTO_INCREMENT,
  seconds INT NOT NULL,
  FOREIGN KEY (fkey_recipe_id) REFERENCES Recipe(id) ON DELETE CASCADE
);

INSERT INTO Theme (theme_name) VALUES
('Light'),
('Dark');

INSERT INTO User (username, password, fkey_theme_id) VALUES
('twpeacemaker','something', 1),
('dksmith','something', 1);



INSERT INTO Units (short_name, name) VALUES
('lbs', 'Pound'),
('oz', 'Ounces'),
('tsp', 'Teaspoon'),
('tbsp', 'Tablespoon'),
('cup', "Cups");
