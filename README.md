# Apollo Clinical

Sistema de gestão para clínicas e consultórios, desenvolvido em PHP puro (sem framework), como projeto de estudo prático para transição de nível N2 → N3.

Projeto irmão do [ApolloStock], reaproveitando padrões de arquitetura e boas práticas já validados naquele projeto.

---

## Sobre o projeto

O Apollo Clinical permite o cadastro de pacientes, profissionais, serviços oferecidos e agendamentos, com módulo de histórico de atendimentos e financeiro básico (em desenvolvimento).

---

## Stack

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.4 (puro, sem framework) |
| Banco de dados | PostgreSQL 18 |
| Servidor web | Nginx + PHP-FPM |
| Front-end | Bootstrap 5 + JavaScript vanilla |
| Ambiente | Debian 13 via WSL |

---

## Arquitetura

### Estrutura de pastas

```
apollo_clinical/
├── config/                   # fora do alcance do navegador
│   ├── .env                   # credenciais reais (não versionado)
│   ├── .env.example            # modelo de variáveis de ambiente
│   └── conexaoBanco.php         # carrega .env e abre conexão PDO
├── src/                        # classes e funções reutilizáveis (em construção)
│   ├── Models/
│   ├── Helpers/
│   ├── Auth/
│   └── Audit/
├── logs/                        # logs de erro da aplicação
└── public/                       # ROOT do Nginx — única pasta acessível via navegador
    ├── index.php
    ├── assets/
    └── pacientes/
        ├── form-cadastrar-pacientes.php
        ├── form-listar-pacientes.php
        ├── form-alterar-pacientes.php
        ├── cadastrarPaciente.php
        ├── editarPaciente.php
        └── excluirPaciente.php
```

### Decisão de arquitetura: separação `public/` x raiz do projeto

O Nginx aponta o `root` apenas para `public/`. Arquivos de configuração (`.env`, conexão com banco) ficam **fora** dessa pasta, tornando-os inacessíveis via URL — mesmo que o PHP-FPM falhe, esses arquivos não são expostos.

### Padrão de organização por módulo

Cada módulo de domínio possui sua própria pasta dentro de `public/`, com arquivos separados por responsabilidade:

```
[modulo]/
├── form-cadastrar-[entidade].php   → formulário HTML
├── cadastrar[Entidade].php          → lógica: validação + INSERT
├── form-listar-[entidade]s.php       → SELECT + tabela HTML
├── form-alterar-[entidade].php        → formulário pré-preenchido
├── editar[Entidade].php                → lógica: validação + UPDATE
└── excluir[Entidade].php                → lógica: DELETE
```

### Conexão com banco via `.env`

As credenciais são carregadas por um parser `.env` implementado manualmente em PHP puro, sem dependência de biblioteca externa. O mecanismo usa `file()` para ler o arquivo linha por linha, `explode('=', $linha)` para separar chave e valor, e `trim()` para remover espaços e quebras de linha — populando `$_ENV` dinamicamente.

---

## Modelagem do banco de dados

15 tabelas. Decisões arquiteturais relevantes:

- **`pessoas`** centraliza dados comuns (nome, CPF, telefone, endereço). As tabelas `pacientes`, `profissionais` e `usuarios` se conectam a ela via `pessoa_id` (FK), evitando duplicidade quando uma mesma pessoa ocupa múltiplos papéis (ex: funcionária que também é paciente).
- **Tabelas de junção** (`profissional_especialidades`, `atendimento_servicos`) resolvem relações N:N com chave primária composta.
- **`servico_precos`** permite que o preço de um serviço varie por profissional e/ou convênio, sem alterar o `preco_base` do catálogo.
- **`agendamentos`** e **`atendimentos`** são tabelas separadas: nem todo agendamento se concretiza (pode ser cancelado). A relação é 1 para 0-ou-1.
- **`ON DELETE RESTRICT`** em dados de histórico clínico e financeiro (agendamentos, atendimentos, pagamentos) — o banco impede exclusões que gerariam perda de histórico.
- **`ON DELETE CASCADE`** apenas em tabelas de junção, onde o vínculo é "parte de" outro registro (ex: especialidades de um profissional excluído).

### Script de criação do banco

```sql
-- Executar conectado ao banco clinical

CREATE TABLE public.pessoas (
    id integer NOT NULL,
    nome character varying(150) NOT NULL,
    cpf character varying(15) NOT NULL,
    telefone character varying(13) NOT NULL,
    email character varying(100),
    cep character varying(10),
    logradouro character varying(50),
    numero character varying(7),
    complemento text,
    bairro character varying(20),
    cidade character varying(20),
    uf character varying(4),
    contato_emergencial character varying(13),
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.convenios (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    codigo character varying(50) NOT NULL,
    ativo boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.especialidades (
    id integer NOT NULL,
    nome character varying(50) NOT NULL,
    descricao text,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.servicos (
    id integer NOT NULL,
    nome character varying(100) NOT NULL,
    descricao text,
    preco_base numeric(10,2) NOT NULL,
    ativo boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.pacientes (
    id integer NOT NULL,
    pessoa_id integer NOT NULL,
    convenio_id integer,
    data_nascimento date NOT NULL,
    observacoes text,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.profissionais (
    id integer NOT NULL,
    pessoa_id integer NOT NULL,
    registro_conselho character varying(200),
    bio text,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    pessoa_id integer NOT NULL,
    login character varying(50) NOT NULL,
    senha_hash character varying(255) NOT NULL,
    perfil character varying(20) NOT NULL,
    ativo boolean DEFAULT true,
    ultimo_acesso timestamp without time zone,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.profissional_especialidades (
    profissional_id integer NOT NULL,
    especialidade_id integer NOT NULL
);

CREATE TABLE public.servico_precos (
    id integer NOT NULL,
    servico_id integer NOT NULL,
    profissional_id integer,
    convenio_id integer,
    preco numeric(10,2) NOT NULL
);

CREATE TABLE public.agendamentos (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    profissional_id integer NOT NULL,
    servico_id integer NOT NULL,
    data_hora timestamp without time zone NOT NULL,
    status character varying(20) DEFAULT 'agendado' NOT NULL,
    observacoes text,
    created_at timestamp without time zone DEFAULT now(),
    CONSTRAINT ck_agendamentos_status CHECK (
        status IN ('agendado','confirmado','em_atendimento','finalizado','cancelado','faltou')
    )
);

CREATE TABLE public.atendimentos (
    id integer NOT NULL,
    agendamento_id integer NOT NULL,
    data_hora_inicio timestamp without time zone NOT NULL,
    data_hora_fim timestamp without time zone,
    anamnese text,
    prescricao text,
    observacoes text,
    created_at timestamp without time zone DEFAULT now()
);

CREATE TABLE public.atendimento_servicos (
    atendimento_id integer NOT NULL,
    servico_id integer NOT NULL,
    preco_cobrado numeric(10,2) NOT NULL
);

CREATE TABLE public.pagamentos (
    id integer NOT NULL,
    atendimento_id integer NOT NULL,
    convenio_id integer,
    valor_total numeric(10,2) NOT NULL,
    forma_pagamento character varying(20) NOT NULL,
    status character varying(20) DEFAULT 'pendente' NOT NULL,
    data_pagamento timestamp without time zone,
    created_at timestamp without time zone DEFAULT now(),
    CONSTRAINT ck_pagamentos_status CHECK (
        status IN ('pendente','pago','cancelado','reembolsado')
    )
);

CREATE TABLE public.audit_log (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    tabela character varying(50) NOT NULL,
    acao character varying(20) NOT NULL,
    registro_id integer NOT NULL,
    dados_anteriores jsonb,
    dados_novos jsonb,
    created_at timestamp without time zone DEFAULT now(),
    CONSTRAINT ck_audit_log_acao CHECK (acao IN ('INSERT','UPDATE','DELETE'))
);
```
---

## Funcionalidades implementadas

### Módulo de Pacientes

| Ação | Arquivo | Status |
|---|---|---|
| Formulário de cadastro | `form-cadastrar-pacientes.php` | ✅ |
| Lógica de cadastro | `cadastrarPaciente.php` | ✅ |
| Listagem | `form-listar-pacientes.php` | ✅ |
| Formulário de edição (pré-preenchido) | `form-alterar-pacientes.php` | ✅ |
| Lógica de edição | `editarPaciente.php` | 🔄 em construção |
| Exclusão | `excluirPaciente.php` | ✅ |

**Detalhes técnicos do módulo:**

- **Cadastro:** 2 INSERTs em sequência (`pessoas` → `pacientes`), usando `lastInsertId('pessoas_id_seq')` para vincular os registros via FK.
- **Listagem:** query com `JOIN` em `pessoas` e `LEFT JOIN` em `convenios` (LEFT porque `convenio_id` é opcional — paciente particular não teria convênio).
- **Edição:** formulário pré-preenchido via SELECT com `WHERE pac.id = :id`, usando prepared statement. UPDATE pendente de finalização.
- **Exclusão:** DELETE restrito à tabela `pacientes` (preserva o registro em `pessoas`, que pode ter outros vínculos). Tratamento de erros por código SQLSTATE:
  - `23503` → FK violation (paciente possui agendamentos vinculados — mensagem específica)
  - genérico → qualquer outro erro de banco

### Em desenvolvimento

- [ ] Finalização do `editarPaciente.php` (2 UPDATEs: `pessoas` + `pacientes`)
- [ ] Módulo de Profissionais (com especialidades N:N)
- [ ] Módulo de Convênios e Serviços
- [ ] Módulo de Agendamentos
- [ ] Módulo de Atendimentos
- [ ] Módulo Financeiro básico
- [ ] Log de auditoria

---

## Boas práticas aplicadas

- **Prepared statements** em todas as queries (`prepare()` + `bindValue()`), prevenindo SQL Injection.
- **Tratamento de erros por código SQLSTATE** — distinção entre tipos de erro do PostgreSQL com mensagens específicas para o usuário.
- **Separação de responsabilidades** — cada arquivo PHP tem uma única função (formulário ou lógica), nunca os dois.
- **Variáveis de ambiente** via parser `.env` próprio, sem biblioteca externa.
- **Debug via log** — erros técnicos vão para `error_log()` (visível no log do Nginx/PHP-FPM), nunca expostos na tela do usuário.

---

*Projeto em desenvolvimento contínuo. Foco em fundamentos sólidos de PHP, SQL relacional e arquitetura de aplicações web sem framework — base para transição para frameworks como Laravel.*