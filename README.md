# Cert-Vault

## Project Overview
Cert-Vault is a certificate management application designed to streamline the handling of digital certificates. This application provides a user-friendly interface for managing certificates effectively and securely.

## Features
- Easy to use interface for certificate management.
- Support for generating and storing certificates.
- Integration with a MySQL database for data storage.
- User authentication and role management for enhanced security.
- Detailed logs of certificate usage and management activities.

## Technologies Used
- **PHP**: For back-end logic and server-side scripting.
- **Blade Templates**: For rendering views in a flexible and efficient manner.
- **MySQL**: For reliable and efficient data storage and retrieval.

## Installation Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/ahmadrizal-baihaqi/Cert-Vault.git
   cd Cert-Vault
   ```
2. Configure your .env file for database connectivity.
3. Run migrations to set up the database:
   ```bash
   php artisan migrate
   ```
4. Start the local development server:
   ```bash
   php artisan serve
   ```

## Usage Guide
- Access the application in your web browser at `http://localhost:8000`
- Follow the instructions to create and manage certificates.

## Contribution Guidelines
- Fork the repository and create a new branch for your feature or bug fix.
- Submit a pull request detailing your changes for review.
- Ensure your contributions adhere to our coding standards and practices.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
