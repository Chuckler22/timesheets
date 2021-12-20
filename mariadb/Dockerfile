FROM mariadb:latest
# Copy content from .sql to Docker mysql container
COPY timesheets.sql /docker-entrypoint-initdb.d/init.sql
EXPOSE 3306