# Proiect Unit Testing în PHP

## Descriere

Acest proiect este destinat demonstrării și implementării unit testing pentru aplicații PHP. 
Proiectul include o serie de tabele pentru gestionarea utilizatorilor, sesiunilor, joburilor, produselor, comenzilor și multe altele.

## Schema Bazei de Date

Proiectul utilizează următoarele tabele:

### Migrations
- `migrations`: Stochează informații despre migrațiile aplicate pe baza de date.

### Users
- `users`: Stochează informații despre utilizatorii aplicației.
- Indici unici pentru email.

### Sessions
- `sessions`: Gestionează sesiunile utilizatorilor.
- Indici pentru `user_id` și `last_activity`.

### Jobs
- `jobs`: Stochează joburile care trebuie procesate de queue.
- Indici pentru `queue`.

### Products and Orders
- `products`: Detalii despre produsele disponibile.
- `orders`: Înregistrările comenzilor plasate de utilizatori.
- `order_items`: Articolele specifice fiecărei comenzi.

### Authentication
- `personal_access_tokens`: Token-uri pentru autentificarea și autorizarea bazată pe API.

### Alte Tabele

În afara tabelelor principale, proiectul utilizează și alte tabele suplimentare care sprijină funcționalități secundare esențiale pentru gestionarea și operarea aplicației:
- **password_reset_tokens**: Acest tabel stochează tokenurile temporare utilizate pentru resetarea parolelor utilizatorilor. Asigură securitatea procesului de resetare a parolei prin verificarea proprietății contului.
- **cache** și **cache_locks**: Tabelele `cache` și `cache_locks` sunt folosite pentru a îmbunătăți performanța aplicației prin stocarea temporară a datelor frecvent accesate și pentru a gestionare a accesului concurent la aceste date cache-ate. Aceasta reduce timpul de încărcare al paginilor și crește eficiența răspunsului aplicației.
- **job_batches**: Acest tabel este folosit pentru a gestiona loturile de joburi în background, permițând urmărirea și gestionarea grupurilor de procese care trebuie executate asincron. Aceasta ajută la monitorizarea stadiului de completare a proceselor batch și la gestionarea erorilor.
- **failed_jobs**: Stochează informații despre joburile care au eșuat în timpul execuției. Acest tabel este crucial pentru depanarea și reîncercarea joburilor eșuate, oferind o istorie detaliată a problemelor care pot apărea în execuția asincronă a sarcinilor.

Aceste tabele ajută la îmbunătățirea securității, performanței și fiabilității aplicației, oferind suport pentru funcții de administrare esențiale și recuperarea în caz de eșec.

## Controllere

### Controller de Bază
- `Controller`: Clasă abstractă de bază pentru toate celelalte controllere.

### CategoryController
- `index()`: Returnează toate categoriile cu produsele lor.
- `show(Category $category)`: Afisează o categorie și produsele asociate.

### UserController
- `orders(User $user)`: Returnează comenzile unui utilizator, împreună cu detaliile produselor comandate.

### ProductController
- `show(Product $product)`: Afisează detalii despre un produs.
- `update(UpdateProductRequest $request, Product $product)`: Actualizează un produs cu datele valide primite.

### OrderController
- `show(Order $order)`: Afisează detalii despre o comandă.
- `update(UpdateOrderRequest $request, Order $order)`: Actualizează starea unei comenzi cu verificări de validitate.
  
## Teste

Proiectul include o serie de teste automate pentru a verifica funcționalitatea corectă a componentelor.
Ca librarie de testare am folosit <b>phpunit</b> fiind default in Laravel.

### Category Tests

Testele pentru categorii sunt concepute pentru a verifica corectitudinea funcțiilor legate de gestionarea categoriilor de produse din aplicație. 
Aceste teste asigură că informațiile despre categorii sunt corect manipulabile și accesibile. Iată detalii despre fiecare test:

- **categories exist**: Acest test verifică existența datelor în tabela de categorii. Se asigură că baza de date conține intrările așteptate pentru categorii, esențiale pentru funcționarea corectă a aplicației. Este crucial pentru a evita erorile legate de lipsa categoriilor atunci când utilizatorii sau alte componente ale aplicației încearcă să acceseze produsele categorizate.

- **get categories list**: Testează funcționalitatea de a recupera o listă a tuturor categoriilor disponibile, împreună cu produsele asociate fiecărei categorii. Acest test este vital pentru funcțiile de afișare a produselor pe categorii în interfața utilizator, asigurând că legăturile între categorii și produsele lor sunt corect reprezentate și că întregul set de date este accesibil.

- **get category**: Focalizează pe verificarea recuperării corecte a detaliilor pentru o categorie specifică, inclusiv a produselor asociate. Acest test este esențial pentru paginile de detalii ale categoriei, unde utilizatorii pot vizualiza toate produsele dintr-o anumită categorie. Verifică dacă informațiile sunt complete și corect formatate, contribuind la o navigație eficientă și la o experiență de utilizare îmbunătățită.

Aceste teste de categorie sunt esențiale pentru orice aplicație de e-commerce sau sistem care se bazează pe o clasificare structurată a informațiilor. Ele asigură funcționarea fără probleme a filtrărilor, căutărilor și afișărilor bazate pe categorie, care sunt critice pentru gestionarea eficientă a inventarului și pentru îmbunătățirea experienței de cumpărături a utilizatorului.


### Order Tests

Testele pentru comenzi sunt esențiale pentru a asigura gestionarea corectă a fluxului de comenzi în cadrul aplicației. Acestea verifică atât logica de creare cât și pe cea de actualizare a comenzilor, concentrându-se în mod special pe validarea schimbărilor de status și pe corectitudinea datelor. Detalii ale testelor includ:

- **order total price**: Verifică dacă prețul total al unei comenzi este calculat corect pe baza prețurilor produselor și a cantităților comandate.
- **order store**: Testează funcționalitatea de creare a unei comenzi noi, verificând dacă toate datele necesare sunt colectate și stocate corect în baza de date.
- **order store product quantity equals stock**: Asigură că nu se pot comanda mai multe produse decât există în stoc.
- **order store invalid user id**, **order store invalid products**, **order store invalid quantity**: Testează răspunsul sistemului la date de intrare invalide, cum ar fi ID-uri de utilizator sau de produse inexistente, și cantități incorecte.
- **get order**: Verifică funcționalitatea de recuperare a detaliilor unei comenzi existente.
- **update order**: Testează actualizarea unei comenzi existente, inclusiv schimbarea statusului acesteia.
- **update order initial new status canceled**, **update order error no data**, **update order error finished new status canceled**, **update order error finished new status initial**: Aceste teste verifică logica de validare a schimbărilor de status ale unei comenzi, asigurându-se că tranzițiile de status sunt permise conform regulilor de business stabilite și că erorile sunt gestionate corespunzător.


### Product Tests

Testele pentru produse sunt concepute pentru a verifica gestionarea corectă a informațiilor despre produse în aplicație.
Aceste teste asigură că produsele pot fi accesate, adăugate, actualizate și manipulate corespunzător.

- **create product**: Verifică funcționalitatea de adăugare a unui nou produs în sistem, asigurându-se că toate atributele necesare sunt corect procesate și stocate.
- **get product**: Testează capacitatea de a recupera informații despre un produs specific, garantând că datele sunt corect afișate utilizatorilor.
- **product get sale price**: Verifică logica de calcul a prețurilor de vânzare, importantă pentru promoții și oferte speciale.
- **product update**: Se concentrează pe actualizarea datelor unui produs, testând adaptabilitatea sistemului la modificările necesare în catalogul de produse.
- **product stock update**: Asigură că actualizările stocului sunt reflectate corect, o componentă crucială pentru gestionarea inventarului.
- **product update error validation**: Verifică robustețea aplicației în fața datelor eronate, asigurându-se că erorile sunt gestionate corespunzător.

### User Tests
Testele pentru utilizatori se concentrează pe funcționalitățile legate de gestionarea și accesul utilizatorilor la propriile comenzi.
Aceste teste sunt vitale pentru asigurarea unei experiențe de utilizare fluide și pentru protejarea datelor utilizatorilor.

- **orders**: Testează capacitatea utilizatorilor de a accesa istoricul propriilor comenzi. Verifică dacă sistemul poate recupera lista de comenzi asociate unui utilizator, inclusiv detalii despre fiecare comandă, cum ar fi statusul, produsele comandate și prețurile. Acest test este crucial pentru funcționalitățile legate de contul de utilizator și pentru asigurarea transparenței procesului de cumpărături.

Aceste teste validează că funcțiile critice pentru utilizatori funcționează așa cum este prevăzut, permițându-le să acceseze și să gestioneze informațiile legate de comenzile lor într-un mod sigur și eficient.

## Compararea Librăriilor de Testare pentru PHP

În dezvoltarea PHP, PHPUnit este adesea alegerea principală pentru testare, datorită integrării sale strânse cu framework-uri populare precum Laravel. Totuși, există alternative care pot fi mai potrivite în funcție de cerințele proiectului.

### PHPUnit
- **Integrare cu Laravel**: Configurat implicit în Laravel.
- **Suport extensiv**: Beneficiază de o comunitate largă și multe resurse.
- **Flexibilitate**: Suportă teste unitare, de integrare și funcționale.
- **Dezavantaje**: Poate fi intimidant pentru începători și uneori verbos.

### PHPSpec
- **Orientare BDD**: Excelent pentru definirea comportamentului aplicației prin BDD.
- **Generare de cod**: Poate genera cod pe baza specificațiilor testelor.
- **Dezavantaje**: Limitat la teste unitare și are o curbă de învățare asociată cu BDD.
  
PHPUnit rămâne ideal pentru majoritatea aplicațiilor PHP, în special cele care utilizează Laravel, în timp ce PHPSpec poate oferi avantaje în scenarii specifice.

## Instrumente de Code Coverage pentru PHP

În dezvoltarea PHP, alegerea instrumentului potrivit pentru code coverage este crucială pentru asigurarea calității și a eficienței testelor. Iată o comparație între 2 instrumente populare: Xdebug si phpdbg.

### Xdebug

**Avantaje:**
- **Detaliat**: Oferă informații amănunțite despre coverage, inclusiv linii neexecutate și apeluri de funcții.
- **Popular**: Foarte cunoscut în comunitatea PHP, integrat bine cu majoritatea IDE-urilor.
- **Funcționalități suplimentare**: Pe lângă code coverage, Xdebug oferă debugging și profiling.

**Dezavantaje:**
- **Performanță**: Poate încetini semnificativ executia aplicației datorită detaliilor pe care le colectează.

### phpdbg

**Avantaje:**
- **Rapid**: Mai rapid decât Xdebug în colectarea datelor pentru code coverage, fără a afecta semnificativ performanța.
- **Simplu**: Include un debugger CLI, fără necesitatea configurării unui server web sau CLI suplimentar.

**Dezavantaje:**
- **Lipsa funcționalităților de debugging avansate**: Nu oferă profiling sau debugging la nivelul lui Xdebug.
- **Popularitate mai redusă**: Poate duce la suport mai slab în anumite medii de dezvoltare.


## Testare prin Mutații cu Infection

Infection este un framework de testare prin mutații pentru PHP care ajută la asigurarea calității codului prin introducerea modificărilor (mutații) 
în codul sursă și verificarea că testele existente pot detecta aceste schimbări. 
Acest proces confirmă robustețea setului de teste, oferind un indicator al calității testelor prin măsurarea ratei la care mutațiile sunt "ucise" 
(detectate și respinse de teste).

### Rezultatele Testării prin Mutații

Utilizând Infection, am realizat următoarele:
- **Mutations Score Indicator (MSI)**: Acest scor reprezintă procentajul de mutații respinse de teste în raport cu totalul mutațiilor generate, 
indicând eficacitatea testelor. În cazul nostru, MSI a fost de 95%, indicând o acoperire excelentă a testelor.
- **Mutation Code Coverage**: 100%, arătând că fiecare linie de cod susceptibilă la mutații a fost testată.
- **Covered Code MSI**: 95%, reflectând procentul de cod acoperit de teste care a respins mutațiile.

## Raportul Testelor Automate

Proiectul include o suită comprehensivă de teste pentru a asigura funcționalitatea corectă a tuturor componentelor. Mai jos este un rezumat al rezultatelor obținute în urma rulării testelor.

### Rezultat Teste

Testele au acoperit următoarele funcționalități principale, confirmând comportamentul așteptat al aplicației:

#### Tests\Feature\CategoryTest
- **categories exist**: Verifică existența categoriilor în baza de date.
- **get categories list**: Asigură că lista de categorii este returnată corect.
- **get category**: Verifică recuperarea detaliilor pentru o categorie specifică.

#### Tests\Feature\OrderTest
- **order total price**: Calculează prețul total al unei comenzi.
- **order store**: Testează crearea unei comenzi noi.
- **update order**: Actualizează o comandă existentă.
- **validate order operations**: Verifică validarea pentru diferite scenarii de creare și actualizare a comenzilor.

#### Tests\Feature\ProductTest
- **create product**: Testează crearea unui produs nou.
- **get product**: Verifică recuperarea detaliilor unui produs.
- **product stock update**: Actualizează stocul unui produs.
- **product update**: Testează actualizarea informațiilor unui produs.

#### Tests\Feature\UserTest
- **orders**: Verifică că comenzile unui utilizator sunt returnate corect.

### Statistici Teste
- **Total teste rulate**: 29
- **Total aserțiuni**: 199
- **Durata totală**: 1.17s

Aceste teste asigură că funcționalitățile critice ale aplicației sunt verificate în mod regulat, reducând riscul de regresie și îmbunătățind calitatea codului.
