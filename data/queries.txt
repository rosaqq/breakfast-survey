CREATE TABLE IF NOT EXISTS aircraft_data (
id SERIAL PRIMARY KEY,
date TEXT,
anomaly TEXT,
side TEXT,
component TEXT,
action TEXT
);

CREATE TABLE IF NOT EXISTS parts_mapping (
id SERIAL PRIMARY KEY,
side TEXT,
component TEXT,
part TEXT,
x TEXT,
y TEXT,
z TEXT,
zone TEXT
);

INSERT INTO parts_mapping (side, component, part, x, y, z, zone) VALUES
('right','Aileron','901231','57.10','4.39','23.29','Asa'),
('right','Flap','901232','17.06','-0.85','10.87','Asa'),
('right','Blindagem','901233','7.80','-0.59','-3.81','Asa'),
('right','Painel Superior','901234','22.01','0.78','1.72','Asa'),
('right','Painel Inferior','901235','31.11','-1.40','4.96','Asa'),
('right','Estabilizador Vertical','901236','1.15','13.59','59.42','Cauda'),
('right','Estabilizador Horizontal','901237','10.82','3.97','65.72','Cauda'),
('right','Nacele','901238','28.87','-3.35','-14.73','Propulsão'),
('right','Pylon','901239','25.28','-0.09','-9.01','Propulsão'),
('right','Fuselagem Dianteira','901240','6.54','3.11','-45.70','Fuselagem'),
('right','Fuselagem Centro','901241','6.73','2.91','-11.53','Fuselagem'),
('right','Fuselagem Traseira','901242','6.34','3.29','24.15','Fuselagem'),
('left','Aileron','901231','-57.10','4.39','23.29','Asa'),
('left','Flap','901232','-17.06','-0.85','10.87','Asa'),
('left','Blindagem','901233','-7.80','-0.59','-3.81','Asa'),
('left','Painel Superior','901234','-22.01','0.78','1.72','Asa'),
('left','Painel Inferior','901235','-31.11','-1.40','4.96','Asa'),
('left','Estabilizador Vertical','901236','-1.15','13.59','59.42','Cauda'),
('left','Estabilizador Horizontal','901237','-10.82','3.97','65.72','Cauda'),
('left','Nacele','901238','-28.87','-3.35','-14.73','Propulsão'),
('left','Pylon','901239','-25.28','-0.09','-9.01','Propulsão'),
('left','Fuselagem Dianteira','901240','-6.54','3.11','-45.70','Fuselagem'),
('left','Fuselagem Centro','901241','-6.73','2.91','-11.53','Fuselagem'),
('left','Fuselagem Traseira','901242','-6.34','3.29','24.15','Fuselagem');

INSERT INTO aircraft_data (date, anomaly, side, component, action) VALUES
('2017','Corrosão','left','Aileron','Remoção'),
('2017','Fratura','left','Flap','Reparação'),
('2017','Corrosão','left','Painel Superior','Substituição'),
('2018','Fratura','left','Painel Inferior','Remoção'),
('2018','Outro','left','Flap','Reparação'),
('2018','Corrosão','left','Blindagem','Substituição'),
('2018','Corrosão','left','Aileron','Remoção'),
('2019','Fratura','left','Flap','Reparação'),
('2019','Corrosão','left','Blindagem','Substituição'),
('2019','Fratura','left','Painel Superior','Remoção'),
('2020','Outro','right','Painel Inferior','Reparação'),
('2020','Corrosão','right','Painel Superior','Substituição'),
('2020','Fratura','right','Painel Inferior','Remoção'),
('2020','Outro','right','Flap','Reparação'),
('2020','Fratura','right','Blindagem','Substituição'),
('2021','Fratura','right','Flap','Remoção'),
('2021','Corrosão','right','Blindagem','Reparação'),
('2021','Fratura','right','Aileron','Substituição'),
('2021','Fratura','right','Painel Superior','Reparação'),
('2021','Corrosão','right','Painel Inferior','Reparação');