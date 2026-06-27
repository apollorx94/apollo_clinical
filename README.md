# Apollo Clinical

Sistema de gestão para clínicas e consultórios, desenvolvido em PHP puro (sem framework), como projeto de estudo prático para transição de nível N2 → N3.

Projeto irmão do [ApolloStock](#) (sistema de gestão de estoque), reaproveitando padrões de arquitetura e boas práticas já validados naquele projeto.

## Sobre o projeto

O Apollo Clinical permite o cadastro de pacientes, profissionais, serviços oferecidos e agendamentos, com módulo de histórico de atendimentos e financeiro básico (em desenvolvimento).

## Stack

- **Linguagem:** PHP 8.4 (puro, sem framework)
- **Banco de dados:** PostgreSQL 18
- **Servidor web:** Nginx + PHP-FPM
- **Front-end:** Bootstrap 5 + JavaScript vanilla
- **Ambiente:** Debian 13 via WSL

## Arquitetura

### Estrutura de pastas

```
apollo_clinical/
├── config/              # fora do alcance do navegador
│   ├── .env              # credenciais reais (não versionado)
│   ├── .env.example       # modelo de variáveis de ambiente
│   └── conexaoBanco.php    # carrega .env e abre conexão PDO
├── src/                  # classes e funções reutilizáveis (em construção)
│   ├── Models/
│   ├── Helpers/
│   ├── Auth/
│   └── Audit/
├── logs/                  # logs de erro da aplicação
└── public/                 # ROOT do Nginx — única pasta acessível via navegador
    ├── index.php
    ├── assets/
    └── pacientes/
        ├── cadastrar-paciente.php
        ├── listar-pacientes.php
        ├── alterarPaciente.php
        └── excluirPaciente.php
```

### Decisão de segurança: separação `public/` x raiz do projeto

O Nginx aponta o `root` apenas para `public/`. Arquivos de configuração (`.env`, conexão com banco) ficam **fora** dessa pasta, evitando exposição via URL.

### Padrão de organização por módulo

Cada módulo de domínio (pacientes, profissionais, convênios...) possui sua própria pasta dentro de `public/`, com um arquivo por ação:

```
[modulo]/
├── cadastrar-[entidade].php   → formulário + INSERT
├── listar-[entidade]s.php      → SELECT + tabela
├── alterar[Entidade].php        → formulário pré-preenchido + UPDATE
└── excluir[Entidade].php          → DELETE
```

## Modelagem do banco de dados

15 tabelas, com destaque para as decisões abaixo:

- **`pessoas`** centraliza dados comuns (nome, CPF, telefone, endereço). `pacientes`, `profissionais` e `usuarios` se conectam a ela via FK (`pessoa_id`), evitando duplicidade de dados quando uma mesma pessoa ocupa múltiplos papéis (ex: funcionário que também é paciente).
- **Tabelas de junção** (`profissional_especialidades`, `atendimento_servicos`) resolvem relações N:N, com chave primária composta.
- **`servico_precos`** permite que o preço de um serviço varie por profissional e/ou convênio.
- **`agendamentos`** e **`atendimentos`** são tabelas separadas: nem todo agendamento se concretiza em atendimento (pode ser cancelado).
- Uso consistente de `ON DELETE RESTRICT` em dados de histórico clínico/financeiro (agendamentos, atendimentos, pagamentos), e `ON DELETE CASCADE` apenas em tabelas de junção onde o vínculo é "parte de" outro registro.

## Funcionalidades implementadas

### Módulo de Pacientes

- [x] **Cadastro** — formulário completo (dados pessoais, endereço, convênio, observações), com `INSERT` em duas tabelas (`pessoas` + `pacientes`) usando `lastInsertId()` para vincular os registros.
- [x] **Listagem** — exibição em tabela com `JOIN`/`LEFT JOIN` entre `pacientes`, `pessoas` e `convenios`.
- [x] **Edição** — formulário pré-preenchido a partir de consulta com `WHERE`, com `UPDATE` em duas tabelas (em finalização).
- [x] **Exclusão** — `DELETE` restrito a `pacientes` (preserva o registro em `pessoas`), com tratamento de erro por código SQLSTATE:
  - `23505` → violação de unicidade (CPF/e-mail duplicado)
  - `23503` → violação de integridade referencial (paciente possui agendamentos vinculados)

### Em desenvolvimento

- [ ] Finalização do UPDATE de pacientes
- [ ] Módulo de Profissionais (com especialidades)
- [ ] Módulo de Convênios e Serviços
- [ ] Módulo de Agendamentos
- [ ] Módulo de Atendimentos
- [ ] Módulo Financeiro básico
- [ ] Log de auditoria

## Práticas técnicas aplicadas

- **Conexão segura:** variáveis de ambiente carregadas via parser `.env` implementado manualmente (sem dependência de biblioteca externa), evitando credenciais hardcoded no código.
- **Prepared statements:** todas as queries usam `prepare()` + `bindValue()`, prevenindo SQL Injection.
- **Tratamento de erros por código SQLSTATE:** distinção entre erros de duplicidade e de integridade referencial, com mensagens específicas para o usuário final.
- **Separação de responsabilidades:** cada arquivo PHP possui uma única responsabilidade (formulário, listagem, alteração ou exclusão).

## Como rodar localmente

1. Clone o repositório dentro de `/var/www/html/`
2. Configure o `config/.env` com base no `config/.env.example`
3. Crie o banco `clinical` no PostgreSQL e execute os scripts de criação das tabelas
4. Configure um virtual host no Nginx apontando para `apollo_clinical/public/`
5. Acesse via navegador

---

*Projeto em desenvolvimento contínuo, com foco em fixação de fundamentos de PHP, SQL e arquitetura de aplicações web sem framework.*
