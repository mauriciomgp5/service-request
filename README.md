<p align="center"><a href="#" target="_blank"><h1>Service Request</h1></a></p>


## About Request Service

Request Service is a web application designed to facilitate the submission and management of change or improvement requests by collaborators and clients. It streamlines the process of collecting, evaluating, and implementing feedback for service enhancements.

### Features

- **Submit Requests:** Collaborators and clients can easily submit requests for changes or improvements to services.
- **Vote on Requests:** Users can vote on requests, indicating whether they like or dislike the proposed changes.
- **Track Status:** Keep track of the status of submitted requests, ensuring transparency and accountability.
- **Commenting System:** Users can add comments to requests for further discussion and clarification.
- **File Attachments:** Attach relevant files to requests to provide additional context or resources.

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/mauriciomgp5/request-service.git
   cd request-service
   
2. **Install dependencies:**

   ```bash
   composer install
   npm install

3. **Set up the environment:**

   ```bash
   cp .env.example .env
   php artisan key:generate

4. **Run migrations:**

   ```bash
   php artisan migrate

5. **Serve the application:**

   ```bash
   php artisan serve

6. **Access the application:**

   Open your web browser and navigate to `http://localhost:8000`.


## Contributing

We welcome contributions to enhance the Request Service. Please fork the repository and create a pull request with your changes.

## License

Request Service is open-sourced software licensed under the MIT license.
