FROM mariadb:latest
# Copy content from .sql to Docker mysql container
COPY timesheet-3.sql /docker-entrypoint-initdb.d/init.sql
EXPOSE 3306