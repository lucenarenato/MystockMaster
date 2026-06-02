# Arquivo, Arquivavel e Volume

## Objetivo

Documentar os novos modelos criados para estender o suporte de anexos/ECM no MyStockMaster sem quebrar o schema atual.

## Modelos

### `App\Models\Arquivo`

- Mapeado para a tabela `arquivos`.
- Campos adicionais esperados como nullable: `sha256`, `extensao`, `caminho`, `unidade`, `volume_id`, `tipo`, `is_privado`, entre outros compatíveis com o schema estendido.
- Relacionamentos:
  - `arquivoRelacionamentos()`: `hasMany(Arquivavel::class, 'arquivo_id')`
  - `volume()`: `belongsTo(Volume::class, 'volume_id')`
- Atributos calculados:
  - `url`: gera URL pública baseada em `sha256`.
  - `src`: gera placeholder de imagem para PDF/vídeo ou usa `url` padrão.
- Métodos úteis:
  - `getCaminho()`: constrói o caminho de storage via `unidade` + `volume` + `id.extensao`, ou devolve `caminho` quando não há volume.
  - `getBinary()`: busca o conteúdo binário via `Storage::disk($volume->disco)` quando há volume associado.
  - `removerArquivo()`: deleta o registro e, se houver volume, remove o arquivo do disco correspondente.

### `App\Models\Arquivavel`

- Mapeado para a tabela `arquivaveis`.
- Relacionamento:
  - `arquivo()`: `belongsTo(Arquivo::class, 'arquivo_id')`
- Serve como adapter para o relacionamento entre entidades anexáveis e o arquivo físico.

### `App\Models\Volume`

- Mapeado para a tabela `volumes`.
- Campos preenchíveis: `disco`, `volume`.
- Usado para representar volumes de storage/discos externos e permitir lookup de disco pelo `volume_id` do `Arquivo`.

## Uso esperado

- Continue usando o fluxo atual de uploads do `Upload` (tabela `arquivos`) para anexos simples.
- Use `Arquivo` quando precisar de metadados adicionais ou de lógica de volume/disco.
- Use `Arquivavel` como ponte para registros de relacionamento polimórfico entre um modelo anexável e o arquivo.

## Migração

A migration `database/migrations/2026_06_02_000001_extend_arquivos_and_arquivaveis_tables.php` adiciona colunas nullable às tabelas existentes para aceitar o novo conjunto de campos sem impactar as cargas atuais.

## Observações

- Campos extras são mantidos como `nullable` para preservar compatibilidade.
- A configuração de disco deve existir em `config/filesystems.php` para que `Storage::disk($volume->disco)` funcione corretamente.
- Não há necessidade de alterar os uploads de clientes existentes enquanto o esquema atual continuar suportado.
