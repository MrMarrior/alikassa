БД: sqlite
Таблицы: 
wallets - хранит баланс и тип валюты
transactions - хранит транзакции, их вспомогатлеьную информацию
Запуск проекта:
1) git clone https://github.com/MrMarrior/alikassa
2) composer install в корне проекта
3) php artisan serve для запуска сервера
API endpoint и примеры тела запроса:
1) /api/deposit 
Raw body:
{
  "user_id": 1,
  "amount": 0.5,
  "tx_hash": "t1i23"
}
2) /api/withdraw
 Raw body:
{
    "user_id": 1,
    "amount": 0.3
}
/api/fee
 Raw body:
{
    "user_id": 1,
    "amount": 0.15
}
