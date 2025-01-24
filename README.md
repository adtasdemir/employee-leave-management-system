
# Leave Request Management System

## Project Description

This project is a **Leave Request Management System** that allows users to submit leave requests, and administrators to manage and approve or reject them. The system has a modular backend built with **Laravel** and a responsive frontend developed using **Vue.js**. It leverages **Docker** for easy deployment and service consistency, providing an efficient environment for both development and production.

### **Technologies Used**

#### Backend:
- **Laravel**: PHP framework for building RESTful APIs and handling business logic.
- **MySQL**: A relational database used to store data.
- **Docker**: Used to containerize the backend, MySQL, and other services for consistent deployment and scalability.
- **Postman**: Used for testing and interacting with the API.

#### Frontend:
- **Vue.js**: JavaScript framework for building interactive user interfaces and dynamic components.
- **Axios**: Used for making API calls to the backend from the frontend.

#### Database:
- **MySQL**: The primary database for both development and testing environments.

---

## Architecture and Design Patterns

The backend follows a **modular architecture** with clear separation of concerns to ensure scalability and maintainability. The application uses the **Service-Repository pattern**, which allows the following components:

- **Repositories**: Abstraction layer for accessing and managing data from the database.
- **Services**: Encapsulates the business logic of the application.
- **Controllers**: Handle incoming API requests, call service methods, and return responses to the client.

### Middlewares:
- **Error Handling Middleware**: Captures unhandled errors and ensures that a standardized error response format is returned.
- **Validation Middleware**: Ensures that incoming requests conform to predefined validation rules to ensure data integrity.

### Models, Migrations, and Seeders:
- **Migrations**: Define and manage changes to the database schema (tables, fields, constraints).
- **Seeders**: Populate the database with initial test data.

---

## Frontend Design

### Pages and Technologies Used:
The frontend is designed to be **responsive** and **user-friendly**, allowing users to interact with leave requests seamlessly.

- **Home Page**: Displays featured leave requests and allows users to view and submit new requests.
- **Admin Page**: Provides administrators with the ability to view user information (CRUD operations) and manage leave requests (approve or reject them).

The frontend uses **Vue.js** for building dynamic and responsive components, and **Axios** is used for communicating with the backend API.

---

## Running the Project

To run the project locally, follow the steps below:

1. **Clone the repository**:
   ```bash
   git clone [<repository-url>](https://github.com/adtasdemir/employee-leave-management-system.git)
   ```

2. **Build and start the Docker containers**:
   ```bash
   docker-compose up --build
   ```
   This command will start the backend, nginx, and MySQL services in their respective containers.

3. **Access the application**:
   - **Frontend**: http://localhost:3000
   - **Backend (API)**: http://localhost:8080

4. **Run migrations and seeders**:
   After starting the Docker containers, run the migrations and seeders to set up the database:

   ```bash
   docker-compose exec backend php artisan migrate --seed
   ```

   This will:
   - Create the necessary database tables.
   - Insert mock data to make sure the application has initial data for testing.

---

## Testing

This project includes **deep unit tests** to ensure that all critical use cases are covered. The tests validate the functionality of the backend API, including leave request creation, approval/rejection, and user leave request listings. **Laravel’s PHPUnit** framework has been used for these tests.

To run the tests, use the following command inside the Docker container:

```bash
docker-compose exec backend php artisan test
```

---

## Postman Collection

The **Postman collection** for the API is included in the project as `Employee Leave Management System.postman_collection`. You can import this collection into Postman to easily test the API endpoints.

---


### Final Notes
In order to improve both the performance and accuracy of leave management, several key enhancements and updates have been made to the project. These updates focus on improving how leave data is handled and ensuring the stability of the application by testing core components involved in the leave request process.

#### Keys:

##### User Model Enhancement
A new field called remaining_annual_leave_days has been added to the User model. This field stores the remaining annual leave days for each user, removing the need to calculate it dynamically from the leave_requests table each time. By storing this value directly, the application avoids unnecessary computations, improving performance when managing leave days.

##### LeaveRequestRepository Logic
The LeaveRequestRepository contains important business logic for handling leave requests. It is responsible for interacting with the database and performing key operations like saving, updating, and querying leave requests. 

##### LeaveRequestControllerTest
The LeaveRequestControllerTest is designed to ensure that the leave request functionality is working as expected at the controller level. It tests (All Cases) the API endpoints that interact with leave requests, validating that the expected outcomes occur when creating, updating, or deleting leave requests. The tests cover various cases, including checking for proper leave day calculations, user validation, and error handling.



