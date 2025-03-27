FROM node:22.14.0-alpine

RUN npm install -g @quasar/cli npm@latest

WORKDIR /app

COPY ./frontend/package*.json ./

RUN npm install

COPY ./frontend .

EXPOSE 8080

CMD [ "quasar", "dev" ]