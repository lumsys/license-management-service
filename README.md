# Centralized License Service

A Laravel-based API service for managing licenses and entitlements across multiple brands.  
This service acts as the **single source of truth** for licenses and provides APIs for brands and end-user products.

## Table of Contents

1. [Overview](#overview)
2. [User Stories](#user-stories)
3. [API Endpoints](#api-endpoints)
4. [Setup and Run Locally](#setup-and-run-locally)
5. [Screenshots](#screenshots)


## 1. Overview

The service provides:

* License provisioning for brands
* License activation and deactivation for end-user products
* License status queries
* Listing licenses per customer email
* Multi-tenant support (brand-specific boundaries)
* Seat enforcement per license

**Tech Stack:**

* PHP 8.2+
* Laravel 10
* MySQL
* API-only service
* Looging
* Scribe OpenApi
* CI

## 2. User Stories

### **US1 – Brand can provision a license**

* Endpoint: `POST /brands/{brand}/licenses`
* Creates a license key and associates licenses to products.
* Each license has a status (`valid`, `suspended`, `cancelled`) and expiration date.
* Supports adding additional licenses to an existing key.

<img width="679" height="446" alt="Brand can provision a license" src="https://github.com/user-attachments/assets/ad7e762c-bd09-4972-99d1-01096e22a758" />


### **US2 – Brand can change license lifecycle**

* Endpoints:
  * `PATCH /licenses/{license}/status` – Change license status (suspend/resume/cancel)
  * `PATCH /licenses/{license}/renew` – Extend license expiration
* Allows brands to manage license lifecycle.

  <img width="686" height="359" alt="Brand can change license lifecycle" src="https://github.com/user-attachments/assets/cd982fdc-f81f-4aff-8b2f-61f497e3bf73" />

  <img width="694" height="423" alt="Brand can renew" src="https://github.com/user-attachments/assets/fb0bf414-0867-4856-b0d1-dca853ad3390" />



### **US3 – End-user product can activate a license**

* Endpoint: `POST /licenses/activate`
* Activates a license for a specific instance (`instance_id`)
* Seat limits are enforced per license.

  <img width="644" height="407" alt="End-user product can activate a license-" src="https://github.com/user-attachments/assets/6f1df935-5da0-45d9-8d52-eae34a9df8f0" />


### **US4 – User can check license status**

* Endpoint: `GET /licenses/{key}`
* Returns:
  * License key
  * Validity
  * Entitlements (per product)
  * Remaining seats
  * Status and expiration
<img width="679" height="439" alt="Can check license status" src="https://github.com/user-attachments/assets/7f612734-a7a1-4b17-8c13-92fd89736a6c" />


### **US5 – End-user product can deactivate a seat**

* Endpoint: `POST /licenses/deactivate`
* Deactivates a specific license activation freeing a seat.

<img width="680" height="364" alt="User can deactivate license" src="https://github.com/user-attachments/assets/a176df50-9ae1-4e51-bd78-256c31c4460f" />

  

### **US6 – Brands can list licenses by customer email**

* Endpoint: `GET /brands/licenses?email={email}`
* Returns all licenses for a given email across the brand.
* Only accessible by brand API keys.

  <img width="685" height="449" alt="Brands can list licenses by customer email-" src="https://github.com/user-attachments/assets/6376113e-9133-44f5-b487-2dee44edce75" />


## 3. API Endpoints

| Method | Endpoint | Description | Authentication |
| ------ | -------- | ----------- | -------------- |
| POST | `/brands/{brand}/licenses` | Provision license key & licenses | API-Key |
| PATCH | `/licenses/{license}/status` | Update license status | API-Key |
| PATCH | `/licenses/{license}/renew` | Renew license | API-Key |
| GET | `/brands/licenses` | List licenses by email | API-Key |
| POST | `/licenses/activate` | Activate a license for an instance | API-Key |
| POST | `/licenses/deactivate` | Deactivate a license instance | API-Key |
| GET | `/licenses/{key}` | Check license status | API-Key |
