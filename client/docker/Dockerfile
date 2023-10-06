FROM node:latest

COPY ./ /app

WORKDIR /app

RUN NODE_ENV=development npm install

RUN npm run build

EXPOSE 3000

