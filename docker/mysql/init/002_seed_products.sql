SET NAMES utf8mb4;

INSERT INTO products (id, name, subtitle, description, image) VALUES
(1, 'Brezžične Slušalke NovaSound', 'Brezžična Avdio Oprema', JSON_ARRAY(
    'NovaSound brezžične slušalke so zasnovane za vsakodnevno uporabo, tako doma kot na poti. Ponujajo uravnotežen zvok z jasnimi visokimi toni in polnimi basi, udobne mehke blazinice pa poskrbijo za prijetno nošenje skozi ves dan.',
    'Baterija zdrži do 20 ur predvajanja, polnjenje pa je hitro in preprosto. Stabilna bluetooth povezava zagotavlja nemoteno predvajanje glasbe, video klicev ali podcastov brez prekinitev.'
), '/public/izdelki/izdelek-1.jpg'),
(2, 'Pametna Ura FitPulse', 'Pametna Nosljiva Naprava', JSON_ARRAY(
    'FitPulse pametna ura je idealna spremljevalka aktivnega življenjskega sloga. Ves dan beleži korake, porabo kalorij in srčni utrip, ponoči pa analizira kakovost vašega spanca.',
    'Zaslon prikazuje obvestila iz telefona, opomnik za gibanje in do deset različnih športnih načinov vadbe. Vmesnik je intuitiven in enostaven za uporabo tudi med aktivnostjo.',
    'Ohišje je narejeno iz lahkih materialov, udobje pri nošenju pa je zagotovljeno z mehkim silikonskim pasom. Ura je primerna za nošenje pri delu, vadbi in vsakodnevnih opravilih.'
), '/public/izdelki/izdelek-2.jpg'),
(3, 'Prenosni Zvočnik WaveMini', 'Prenosna Zvočna Rešitev', JSON_ARRAY(
    'WaveMini je kompakten prenosni zvočnik, ki ga zlahka vzamete kamorkoli.',
    'Kljub majhni velikosti ponuja poln zvok z izrazitimi basi in čistimi vokali.',
    'Odpornost IPX5 ščiti pred škropljenjem, zato je zanesljiv ob bazenu ali na terasi.',
    'Baterija omogoča več ur neprekinjenega predvajanja za vsako priložnost.'
), '/public/izdelki/izdelek-3.jpg'),
(4, 'Tipkovnica ProType', 'Profesionalna Vhodna Oprema', JSON_ARRAY(
    'ProType mehanska tipkovnica je namenjena vsem, ki tipkajo veliko in cenijo natančen odziv.',
    'Tiha stikala zmanjšajo hrup in so primerna za pisarne ali pozne večerne ure dela.',
    'Osvetlitev tipk izboljša vidljivost pri slabi svetlobi in doda eleganten videz delovni mizi.',
    'Kakovostna gradnja zagotavlja dolgo življenjsko dobo in stabilen, udoben občutek pri tipkanju.'
), '/public/izdelki/izdelek-4.jpg'),
(5, 'Miška Glide X', 'Ergonomska Kontrolna Naprava', JSON_ARRAY(
    'Glide X je ergonomska brezžična miška, oblikovana za dolge ure udobnega dela z računalnikom.',
    'Natančen optični senzor zagotavlja gladko premikanje kazalca na različnih površinah brez zamikov.',
    'Tihi kliki zmanjšajo moteč hrup in so prijazni do okolice v skupnih prostorih.',
    'Hitro polnjenje in dolga avtonomija baterije poskrbita, da miška nikoli ne bo na poti.'
), '/public/izdelki/izdelek-5.jpg')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    subtitle = VALUES(subtitle),
    description = VALUES(description),
    image = VALUES(image);
