# News Aggregator API - README

Welcome to the News Aggregator API! This application aggregates news articles from various sources and offers a convenient API to search and filter through them. It is containerized using Docker.

## Prerequisites

To run this project, you must have git, docker, and docker-compose installed on your machine:

-   **Git** to clone the repository.
-   **Docker and Docker Compose** to build containers

Any recent versions of docker and docker-compose should work. However, their versions on my machine are:

-   **Docker version** 27.2.0, build 3ab4256
-   **Docker Compose version** v2.29.2-desktop.2

## Quick Start Guide

1. **Clone the Repository**

    Start by cloning the Git repository into a new directory:

    ```sh
    mkdir aw-news-aggregator-api
    cd aw-news-aggregator-api
    git clone https://github.com/awadood/aw-backend-dev-news-aggr.git .
    ```

2. **Run Docker Compose**

    Run the following command to build and start all services.

    ```sh
    docker-compose up --build -d
    ```

    This will set up the containers for:

    - **aw_news_aggregator**: Running the Laravel backend.
    - **nginx_server**: Serving as the web server.
    - **postgres_db**: Storing the application data.
    - **redis_cache**: For caching.

3. **Access the Application**

    After the services are up and running, you can access the application by navigating to:

    - [http://localhost:8080](http://localhost:8080) - This URL will be served by Nginx and the landing page will help you to view the **Documentation** and **Code Coverage Report** as shown in screenshots.

4. **Stopping the Containers**

    To stop and remove the running containers, use the following command:

    ```sh
    docker-compose down
    ```

    This command will stop all running services and remove the containers. Data in PostgreSQL and Redis will be persistent across runs due to the use of Docker volumes.

## Screenshots

![Homepage](./public/homepage.png)

![API Documentation](./public/documentation.png)

![Code Coverage](./public/code-coverage.png)

## Important Configuration Files

1. **articles.php**: This configuration file manages article-related settings, such as the number of articles to fetch, cron expressions, and caching options.

2. **fractal.php**: This configuration file handles the transformer settings for the application, defining how different data types and models are transformed before being returned by the API.

## Highlights

This application is designed to showcase my various expertise in developing efficient, modular, and scalable solutions. Here are some of the highlights that make this project stand out:

1. **Optimized Database Schema with Attributes Table**: The schema leverages a flexible design where attributes of articles are stored in a separate table, making it easy to filter articles based on dynamic characteristics. This design also offers scalability and allows easy addition of new attributes without altering the core schema, making it highly extensible.

2. **Pipeline Design for Fetching Articles**: I implemented a pipeline design pattern to fetch, transform, and store articles. This modular pipeline structure allows for easy extension as new data sources are integrated and makes the solution easy to maintain and providing clear separation of responsibilities. The fetcher classes themselves are designed to be easily configurable, making it straightforward to add, modify, or remove data sources.

3. **Optimized Insertion of Articles into Database**: I used a combination of hash checks and caching to ensure that duplicate articles are not inserted, significantly optimizing the insertion process, reducing redundancy, and ensuring the accuracy.

4. **Exception Handling**: I added a custom exception (`FetchFailedException`) for better error reporting, and rendering.

5. **Redis Caching**: I used Redis for caching to prevent duplicate API requests to external news services, reducing unnecessary data traffic and speeding up responses.

6. **Comprehensive API Documentation**: I added Swagger-based documentation for all endpoints, along with the standard code comments and non-documentational comments. This combination enables smooth onboarding for developers and ensures the documentation stays up-to-date with the implementation.

7. **Code Coverage Reports**: I included automated test coverage with reports that show which parts of the codebase are covered by tests.

8. **CSS and Asset Configuration in Docker**: I debugged and resolved issues with serving CSS and assets through Nginx, ensuring the coverage report and other assets display correctly.

## Contact

If you have any questions or run into any issues, you can reach me at **[awadoodraj@hotmail.com](mailto:awadoodraj@hotmail.com)** or **+49 151 5789 7159**.
