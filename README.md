# Business Connect

## Project Description

This API is a system designed for users to manage and publish information about their businesses. The primary goal is to provide a platform where business owners can register their businesses, add relevant information, and offer services and products to their customers. Additionally, the API includes features for customers to interact with businesses through reviews and information requests. While payment functionality is not currently enabled, the API is built to be extended in the future.

---

## **Main Features**

### **Business Management**
- Users can register their businesses by providing key information such as:
  - Business name.
  - Description.
  - Contact details (phone, email, etc.).
  - Address.
  - Operating hours.
  - Social media links.
  - Covered areas.
- Businesses can include specific and customizable characteristics.

### **Services and Products Management**
- Users can add services and products associated with their businesses.
- Customers can view detailed information about the services and products offered.

### **Review System**
- Customers can generate reviews for:
  - Businesses.
  - Services.
  - Products.
- Reviews allow customers to share their experiences and rate what the business offers.

### **Information Requests**
- The API includes a system for customers to request specific information about a business, service, or product through a form.
- These requests are managed by the business owners, who can respond directly.

### **Future-Ready Design**
- While payment functionality is not currently enabled, the API is designed to be extensible and allow integration with payment systems in future versions.

---

## **Project Objective**

The purpose of this API is to provide an efficient and flexible solution for business owners to manage their online presence. By centralizing information about businesses, services, products, reviews, and requests in one place, it aims to facilitate interaction between business owners and their customers. Additionally, the modular design ensures that the system can evolve with new features as needed.


## Prerequisites

- [DDEV](https://ddev.readthedocs.io/en/stable/) installed on your system
- [Docker](https://www.docker.com/get-started) installed and running

## Local Environment Setup

Follow these steps to set up the project in your local environment:

1. Clone the repository: https://github.com/tantrumTP/business-connect.git
2. Start DDEV: ddev start
3. Install PHP dependencies: ddev composer install
4. Generate Laravel application key: ddev exec php artisan key:generate
5. Run database migrations: ddev exec php artisan migrate
6. Create the storage symbolic link: ddev exec php artisan storage:link
7. (Optional) If you want to populate the database with test data: ddev exec php artisan db:seed --class=DatabaseSeeder