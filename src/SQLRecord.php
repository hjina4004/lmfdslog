<?php

namespace Lmfriends\Lmfdslog;

class SQLRecord
{
  /**
   * @var string
   */
  private $table;

  /**
   * @var array|string[]
   */
  private $defaultColumns;

  /**
   * @var array
   */
  private $additionalColumns;

  /**
   * @param string $table
   * @param array $additionalColumns
   */
  public function __construct(string $table, array $additionalColumns = [])
  {
    $this->table = $table;

    $this->defaultColumns = [
      'id',
      'channel',
      'level',
      'level_name',
      'message',
      'created_at',
    ];

    $this->additionalColumns = $additionalColumns;
  }

  /**
   * @param array $content
   * @return array
   */
  public function filterContent(array $content): array
  {
    return array_filter($content, function ($key) {
      return in_array($key, $this->getColumns());
    }, ARRAY_FILTER_USE_KEY);
  }

  /**
   * @return string
   */
  public function getTable(): string
  {
    return $this->table;
  }

  /**
   * @return array|string[]
   */
  public function getDefaultColumns()
  {
    return $this->defaultColumns;
  }

  /**
   * @return array
   */
  public function getAdditionalColumns(): array
  {
    return $this->additionalColumns;
  }

  /**
   * @return array
   */
  public function getColumns(): array
  {
    return array_merge($this->defaultColumns, $this->additionalColumns);
  }
}
