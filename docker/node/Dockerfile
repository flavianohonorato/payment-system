FROM node:20-alpine

WORKDIR /app

RUN apk add --no-cache wget

# Garantir que o servidor responda a solicitações de fora
ENV HOST=0.0.0.0
ENV PORT=8080

EXPOSE 8080

CMD ["sh", "-c", "echo 'Waiting for npm run dev to be executed from docker-compose'"]