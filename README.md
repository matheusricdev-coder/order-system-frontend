# Order System Frontend

Interface web moderna para um marketplace com autenticação, catálogo de produtos, carrinho persistente e criação de pedidos. Este projeto foi construído com foco em experiência de usuário, boas práticas de frontend e arquitetura escalável para integração com APIs reais.

## Visão Geral

O **Order System Frontend** simula um fluxo real de e-commerce:

- Navegação por catálogo com filtros por categoria e busca.
- Página de produto com galeria e ação de compra.
- Carrinho lateral com persistência no `localStorage`.
- Login/cadastro com integração de autenticação via token.
- Criação de pedidos conectada ao backend.
- Gamificação com barra de login diário e saldo de moedas.

Esse projeto é ideal para portfolio porque demonstra:

- Organização por domínio (`pages`, `components`, `contexts`, `hooks`, `lib`).
- Consumo de API com tratamento de erros.
- Gerenciamento de estado local + estado assíncrono (React Query).
- UI consistente com Tailwind + componentes reutilizáveis.

## Stack Técnica

- **React 18** + **TypeScript**
- **Vite**
- **React Router DOM**
- **TanStack React Query**
- **Tailwind CSS**
- **shadcn/ui** + **Radix UI**
- **Vitest** + Testing Library
- **ESLint**

## Arquitetura do Projeto

```bash
src/
  components/      # componentes visuais e blocos de UI
  contexts/        # estado global (auth e carrinho)
  hooks/           # hooks de integração com API e regras de negócio
  lib/             # cliente HTTP e utilitários
  pages/           # telas principais da aplicação
  types/           # contratos tipados da API
  test/            # setup e testes
```

## Funcionalidades Implementadas

### 1) Catálogo e Busca
- Listagem de produtos com skeleton loading.
- Filtro por categoria.
- Busca textual por produtos.

### 2) Produto
- Galeria com imagem principal e miniaturas.
- Fallback visual quando não há imagem.
- Adição ao carrinho com feedback de toast.

### 3) Carrinho
- Carrinho lateral (drawer) acessível em toda a aplicação.
- Incremento/decremento de quantidade.
- Remoção de itens.
- Cálculo automático de subtotal.
- Persistência em `localStorage`.

### 4) Autenticação
- Login e cadastro com validações de formulário.
- Armazenamento de token.
- Reidratação de sessão ao abrir a aplicação.
- Logout com limpeza de sessão local.

### 5) Pedidos
- Criação de pedido a partir do carrinho.
- Integração preparada para listagem, pagamento e cancelamento.

## Integração com API

A aplicação usa variável de ambiente para conexão com backend:

```bash
VITE_API_BASE_URL=http://localhost:3000
```

### Endpoints esperados

- `POST /auth/login`
- `POST /auth/register`
- `POST /auth/logout`
- `GET /me`
- `GET /me/login-streak`
- `GET /categories`
- `GET /products`
- `GET /products/:id`
- `POST /orders`
- `GET /orders`
- `GET /orders/:id`
- `POST /orders/:id/pay`
- `POST /orders/:id/cancel`

## Como Rodar Localmente

### Pré-requisitos
- Node.js 18+
- npm 9+

### Passo a passo

```bash
# 1) Instalar dependências
npm install

# 2) Criar variáveis de ambiente
cat > .env <<'ENV'
VITE_API_BASE_URL=http://localhost:3000
ENV

# 3) Iniciar em modo desenvolvimento
npm run dev
```

A aplicação ficará disponível em `http://localhost:5173`.

## Scripts Disponíveis

```bash
npm run dev         # ambiente de desenvolvimento
npm run build       # build de produção
npm run preview     # preview local do build
npm run lint        # validação estática com ESLint
npm run test        # testes com Vitest
npm run test:watch  # testes em modo observação
```

## Qualidade e Boas Práticas

- Tipagem forte para payloads e respostas de API.
- Separação de responsabilidade entre UI, hooks e camada HTTP.
- Tratamento de erro padronizado com classe `ApiError`.
- Feedback visual para estados de loading/erro/sucesso.

## Melhorias Recomendadas (Roadmap)

- Cobertura de testes para fluxos críticos (auth, carrinho e checkout).
- Implementação de favoritos (botão já presente no card).
- Controle de paginação do catálogo no frontend.
- Estratégia de i18n para múltiplos idiomas.
- Métricas de performance (Web Vitals) e monitoramento de erros.

## Capturas de Tela

> Sugestão: adicione imagens reais do projeto em execução para reforçar o impacto do portfolio.

- Home (catálogo + filtros)
- Página de produto
- Tela de login/cadastro
- Carrinho aberto

## Autor

Desenvolvido como projeto de portfolio para demonstrar capacidade em:

- Frontend moderno com React + TypeScript
- Integração profissional com APIs
- Estrutura de projeto pronta para evolução em ambiente real

---

Se quiser, posso também preparar uma versão deste README em inglês (focada para recrutadores internacionais) e uma seção “Case Study” com decisões técnicas e trade-offs.
