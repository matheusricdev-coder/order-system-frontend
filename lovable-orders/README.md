# Ordem System — Frontend

Interface web do sistema de pedidos, construída com React + TypeScript + Vite.

## Tecnologias

- **React 18** + **TypeScript**
- **Vite 5** (dev server na porta 8080 com proxy para o backend)
- **Tailwind CSS** + **shadcn/ui**
- **TanStack Query** (cache e fetch de dados)
- **React Router DOM** (roteamento)
- **Vitest** + **Testing Library** (testes)

## Pré-requisitos

- Node.js 20+
- Backend Laravel rodando em `http://127.0.0.1:8000` (desenvolvimento)

## Instalação e execução

```sh
# Instalar dependências
npm install

# Iniciar servidor de desenvolvimento (http://localhost:8080)
npm run dev

# Build de produção
npm run build

# Rodar testes
npm test
```

## Variáveis de ambiente

Crie um arquivo `.env.local` na raiz desta pasta:

```env
# URL base da API (sem barra final)
# Em desenvolvimento o proxy do Vite redireciona /api → http://127.0.0.1:8000
# Em produção aponte para a URL do Railway
VITE_API_BASE_URL=
```

## Deploy

O deploy é feito automaticamente via GitHub Actions para a **Vercel** a cada push na branch `main`.
Consulte `.github/workflows/ci-cd.yml` na raiz do repositório.
