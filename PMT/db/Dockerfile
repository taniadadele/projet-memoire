# Use an official MySQL image as a parent image
FROM  mysql/mysql-server:8.0

# Set environment variables for database configuration
ENV MYSQL_ROOT_PASSWORD="root"
ENV MYSQL_DATABASE="test"
ENV MYSQL_USER="root"
ENV MYSQL_PASSWORD="root"


# Copy the SQL initialization script into the container
COPY ./db/promethee.sql /docker-entrypoint-initdb.d/