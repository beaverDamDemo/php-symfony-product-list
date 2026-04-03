INSERT INTO products (id, name, subtitle, description, image) VALUES
(1, 'Brezzicne Slusalke NovaSound', 'Brezzicna Avdio Oprema', JSON_ARRAY(
    'NovaSound brezzicne slusalke so zasnovane za vsakodnevno uporabo doma ali na poti.',
    'Baterija zdrzi do 20 ur predvajanja in ponuja stabilno bluetooth povezavo.'
), '/public/izdelki/izdelek-1.jpg'),
(2, 'Pametna Ura FitPulse', 'Pametna Nosljiva Naprava', JSON_ARRAY(
    'FitPulse pametna ura belezi korake, kalorije, srcni utrip in spanec.',
    'Prikazuje obvestila in podpira vec sportnih nacinov vadbe.'
), '/public/izdelki/izdelek-2.jpg'),
(3, 'Prenosni Zvocnik WaveMini', 'Prenosna Zvocna Resitev', JSON_ARRAY(
    'WaveMini je kompakten prenosni zvocnik z izrazitimi basi.',
    'IPX5 odpornost ga naredi primernega za zunanjo uporabo.'
), '/public/izdelki/izdelek-3.jpg'),
(4, 'Tipkovnica ProType', 'Profesionalna Vhodna Oprema', JSON_ARRAY(
    'ProType mehanska tipkovnica ponuja natancen odziv in udobje pri tipkanju.',
    'Osvetlitev tipk izboljsa vidljivost in videz delovne mize.'
), '/public/izdelki/izdelek-4.jpg'),
(5, 'Miska Glide X', 'Ergonomska Kontrolna Naprava', JSON_ARRAY(
    'Glide X je ergonomska brezzicna miska za dolgotrajno delo.',
    'Natancen senzor in tihi kliki jo naredijo idealno za pisarne.'
), '/public/izdelki/izdelek-5.jpg')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    subtitle = VALUES(subtitle),
    description = VALUES(description),
    image = VALUES(image);
