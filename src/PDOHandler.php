<?php

namespace Lmfriends\Lmfdslog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PDO;
use PDOStatement;

class PDOHandler extends AbstractProcessingHandler
{
  /**
   * pdo object of database connection
   *
   * @var PDO
   */
  protected $pdo;

  /**
   * statement to insert a new record
   *
   * @var PDOStatement
   */
  private $statement;

  /**
   * @var SQLRecord
   */
  private $sqlRecord;

  /**
   * @param PDO $pdo PDO Connector for the database
   * @param string $table Table in the database to store the logs in
   * @param array $additionalFields Additional Context Parameters to store in database
   * @param bool|int $level Debug level which this handler should store
   * @param bool $bubble
   */
  public function __construct(
    PDO $pdo,
    string $table,
    array $additionalFields = [],
    int $level = Logger::DEBUG,
    bool $bubble = true
  ) {
    parent::__construct($level, $bubble);

    $this->pdo = $pdo;
    $this->sqlRecord = new SQLRecord($table, $additionalFields);
  }

  /**
   * @param array $content
   */
  private function prepareStatement(array $content): void
  {
    $columns = '';
    $fields = '';

    foreach (array_keys($content) as $key => $f) {
      if ($f == 'id') {
        continue;
      }

      if (empty($columns)) {
        $columns .= $f;
        $fields .= ":{$f}";
        continue;
      }

      $columns .= ", {$f}";
      $fields .= ", :{$f}";
    }

    $this->statement = $this->pdo->prepare(
      "INSERT INTO `{$this->sqlRecord->getTable()}` ({$columns}) VALUES ({$fields});"
    );
  }

  /**
   * @param  array $record
   * @return void
   */
  protected function write(array $record): void
  {
    if (isset($record['extra'])) {
      $record['context'] = array_merge($record['context'], $record['extra']);
    }

    $content = $this->sqlRecord->filterContent(array_merge([
      'channel' => $record['channel'],
      'level' => $record['level'],
      'level_name' => $record['level_name'],
      'message' => $record['message'],
      'created_at' => $record['datetime']->format('Y-m-d H:i:s'),
    ], $record['context']));

    $this->prepareStatement($content);

    $this->statement->execute($content);
  }
}
