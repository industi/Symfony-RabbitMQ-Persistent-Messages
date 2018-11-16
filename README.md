# Symfony-RabbitMQ-Persistent-Messages

Overriding Amqp Transport to save messages as persistent (devivery_mode=2)

Steps to reproduce:

git clone https://github.com/industi/Symfony-RabbitMQ-Persistent-Messages.git

composer update

cp .env.dist .env

php bin/console CreateTestMessageCommand [optional --size={messages amount}]

php bin/console messenger:consume-messages test
