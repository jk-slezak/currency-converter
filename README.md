# currency-converter
Application for calculating currency conversion using currency rates from NBP Web API. Using PHP and MySQL database.

# How to run with Docker
1. Clone repository
2. Build custom php image with: `docker build -t my-php:1.0 .`
3. Run docker-compose: `docker-compose up -d`
4. Go to: `http://localhost:8100/`