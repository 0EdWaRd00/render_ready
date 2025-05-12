FROM php:8.2-cli
WORKDIR /app
COPY . .
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev
CMD ["./start.sh"]
