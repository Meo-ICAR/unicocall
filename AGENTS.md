# Specifiche di Progetto: UnicoCall — Call Center Management App

Documento di riferimento per il vibe coding. Descrive l'architettura reale, i modelli dati implementati e le convenzioni del progetto.

---

## 1. Panoramica del Progetto

Applicazione SaaS multi-tenant per la gestione delle operazioni di un Call Center (e più in generale di aziende di intermediazione/consulenza). Il sistema permette a diverse aziende (Tenant) di gestire clienti/lead, dipendenti, sedi, fatturazione attiva e passiva, siti web e conformità GDPR.

---

## 2. Stack Tecnologico

| Pacchetto            | Versione                          |
| -------------------- | --------------------------------- |
| PHP                  | 8.4                               |
| Laravel              | 13                                |
| Filament             | 5.5                               |
| Livewire             | 4                                 |
| Spatie Media Library | 11.x                              |
| Spatie Activity Log  | 5.x                               |
| PhpSpreadsheet       | (via ExcelImportService)          |
| Database default     | SQLite (dev) / MySQL / PostgreSQL |

---

## 3. Architettura Multi-Tenancy

- **Tenant Model:** `Company` (UUID come primary key)
- **Scoping:** Gestito nativamente da Filament tramite `->tenant(Company::class)` in `AdminPanelProvider`
- **Pivot:** `company_user` con campo `role` (default `user`), soft deletes
- **Isolamento dati:** `company_id` su ogni tabella tenant-aware
- **Profilo tenant:** `EditCompanyProfile` page

---

## 4. Modelli Dati

### `Company` (Tenant)

- UUID primary key
- Campi: `name`, `vat_number`, `sponsor`, `company_type` (enum: mediatore, call center, hotel, sw house), `is_iso27001_certified`, `contact_email`, `dpo_email`, `page_header`, `page_footer`, `smtp_*`, `payment`, `payment_frequency`, `payment_startup`, `payment_last_date`
- Traits: `HasUuids`, `SoftDeletes`
- Relazioni: `BelongsToMany` User, `HasMany` Client, Employee, Branch, Registration, Website, SalesInvoice, PurchaseInvoice, Address (morph)

### `User`

- Implements `HasTenants` (Filament multi-tenancy)
- PHP 8 attribute-based `#[Fillable]` e `#[Hidden]`
- Relazioni: `BelongsToMany` Company (pivot con `role`)

### `Client`

- Anagrafica clienti/lead con esteso supporto GDPR e AML
- Traits: `InteractsWithMedia` (Spatie), `LogsActivity` (Spatie — `logUnguarded()->logOnlyDirty()`)
- Campi booleani chiave: `is_person`, `is_lead`, `is_client`, `is_pep`, `is_sanctioned`, `is_remote_interaction`, `is_anonymous`, `is_approved`, `is_ghost`, `is_structure`, `is_regulatory`, `is_company_consultant`, `is_consultant_gdpr`, `is_art108`
- Campi GDPR: `general_consent_at`, `privacy_policy_read_at`, `consent_special_categories_at`, `consent_sic_at`, `consent_marketing_at`, `consent_profiling_at`
- Campo funnel BPM: `status` (default `raccolta_dati`)
- Relazioni: `BelongsTo` Company, ClientType; `BelongsTo` Client (self-ref `leadsource_id`); `HasMany` SalesInvoice (via `partita_iva` ↔ `vat_number`)

### `Employee`

- Struttura gerarchica: `supervisor()` e `subordinates()` (self-referential via `coordinated_by_id`)
- Relazioni: `BelongsTo` Company, Branch (`company_branch_id`), User

### `Branch`

- Sedi aziendali con flag `is_main_office`
- Relazioni: `BelongsTo` Company; `HasMany` Employee

### `SalesInvoice` (Fatture Attive)

- UUID primary key, `SoftDeletes`
- Campi SDI: `numero`, `id_sdi`, `nome_file`, `data_invio`, `data_documento`, `tipo_documento`, `tipo_cliente`
- Campi IVA: `totale_imponibile`, `totale_iva`, `totale_documento`, `netto_a_pagare` + breakdown N1–N7
- Stato incasso: `incassi` (Incassata / Non incassata), `data_incasso`
- Relazione client via `partita_iva` ↔ `vat_number`
- Scopes: `byCompany`, `byStato`, `byCliente`, `byPeriod`, `paid`, `unpaid`
- Accessors: `totale_imponibile_formatted`, `totale_documento_formatted`, `netto_a_pagare_formatted`, date formatted

### `PurchaseInvoice` (Fatture Passive)

- Struttura analoga a `SalesInvoice`
- Campi specifici: `fornitore`, `data_ricezione`, `pagamenti` (Pagata / Non pagata), `data_pagamento`
- Metodi: `markAsRead()`, `markAsPaid()`
- Scopes: `paid`, `unpaid`, `read`, `unread`

### `Address`

- Relazione polimorfica (`addressable_type` / `addressable_id`) — supporta Company e Client
- `SoftDeletes`
- Accessor: `full_address`
- Scopes: `byType`, `forModel`

### `Registration`

- Relazione polimorfica (`registrable_type` / `registrable_id`) — email, PEC, indirizzo telematico
- `SoftDeletes`
- Campi: `registration_type`, `value`, `start_at`, `end_at`, `notes`, `company_id`
- Scopes: `byType`, `active`, `forCompany`

### `Website`

- Conformità siti web aziendali
- Campi: `is_active`, `is_typical`, `is_footercompilant`, `is_iso27001_certified`, `privacy_date`, `transparency_date`, `privacy_prior_date`, `transparency_prior_date`
- Relazioni: `BelongsTo` Company, Client (`clienti_id`)

---

## 5. Filament Admin Panel

**Path:** `/admin` — unico pannello, default tenant.

### Risorse implementate (in `app/Filament/Admin/Resources/`)

Ogni risorsa segue la struttura: `{Resource}.php` + `Pages/` + `Schemas/` (form) + `Tables/`.

| Risorsa                      | Descrizione                                                                 |
| ---------------------------- | --------------------------------------------------------------------------- |
| `CompanyResource`            | Gestione aziende (non scoped al tenant — admin level)                       |
| `ClientResource`             | Anagrafica clienti/lead con tabs (Anagrafica, Privacy, Documenti, Avanzato) |
| `EmployeeResource`           | Gestione dipendenti con gerarchia                                           |
| `BranchResource`             | Sedi aziendali                                                              |
| `RegistrationResource`       | Registrazioni (email, PEC, indirizzi telematici)                            |
| `WebsiteResource`            | Siti web e conformità                                                       |
| `AppSalesInvoiceResource`    | Fatture attive (import + gestione)                                          |
| `AppPurchaseInvoiceResource` | Fatture passive (import + gestione)                                         |

### Pagine custom

- `EditCompanyProfile` — profilo del tenant corrente

---

## 6. Servizi

### `ExcelImportService`

Importa dati da file Excel (`.xls`/`.xlsx`) tramite PhpSpreadsheet.

- `importCompanies(string $filePath, ?string $companyId)` — importa aziende con indirizzi e registrazioni
- `importClients(...)` — importa clienti/lead
- Gestisce validazione, errori per riga, contatori `imported`/`skipped`

### `SalesInvoiceImportService`

Importa fatture attive da Excel o ZIP contenente più file.

### `PurchaseInvoiceImportService`

Importa fatture passive da Excel o ZIP contenente più file.

---

## 7. Console Commands

| Comando                                     | Descrizione                                        |
| ------------------------------------------- | -------------------------------------------------- |
| `import:companies {file=ReportClienti.xls}` | Importa aziende da Excel in `storage/app/private/` |
| `import:sales-invoices {file}`              | Importa fatture attive                             |
| `import:purchase-invoices {file}`           | Importa fatture passive                            |

---

## 8. Routing

- `/` → redirect a `/admin`
- Tutto il resto è gestito da Filament

---

## 9. Convenzioni di Codice

- **PHP 8.4** — constructor property promotion, named arguments, `#[Attribute]` dove applicabile
- **Modelli:** `$guarded = []` oppure `$fillable` esplicito; `casts()` come metodo (non proprietà)
- **Soft Deletes** su: Company, Client, Address, AddressType, Registration, SalesInvoice, PurchaseInvoice
- **UUID** su: Company, SalesInvoice, PurchaseInvoice
- **Nomi italiani** per campi SDI/fatturazione (es. `totale_imponibile`, `partita_iva`, `fornitore`)
- **Filament Resources:** separazione netta tra Schema (form), Table e Pages
- **Activity Log:** `logUnguarded()->logOnlyDirty()` — solo su Client per ora
- **Media Library:** disk `local`/`public`, limite 10MB

---

## 10. Funzionalità da Sviluppare (Backlog)

- Dashboard con widget metriche per tenant (lead da chiamare, tasso conversione, chiamate effettuate)
- `CampaignResource` — gestione campagne outbound/inbound
- `InteractionResource` — log chiamate con player audio (Media Library `recordings`)
- `ScriptResource` — editor WYSIWYG per script operatori
- Action Filament "Esita Chiamata" — modale che crea Interaction e aggiorna stato Lead in transazione
- Copertura test (attualmente minimale)
