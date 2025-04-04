TRUNCATE TABLE metodos_costeo RESTART IDENTITY CASCADE;

INSERT INTO metodos_costeo (descripcion) VALUES 
('PEPS - Primeras Entradas, Primeras Salidas'),
('Promedio Ponderado'),
('Costo Identificado'),
('Costo Estándar');
