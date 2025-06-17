<p align="center">
    <img src="./logo.svg" width="300">
</p>

<div align="center">

[![Total Downloads](https://poser.pugx.org/kseven/userstamps/downloads)](https://packagist.org/packages/kseven/userstamps)
[![Latest Stable Version](https://poser.pugx.org/kseven/userstamps/v)](https://packagist.org/packages/kseven/userstamps)
[![License](https://poser.pugx.org/kseven/userstamps/license)](https://packagist.org/packages/kseven/userstamps)

</div>

## Sobre o Laravel Userstamps

O Laravel Userstamps fornece uma *trait* para Eloquent que mantém automaticamente as colunas `created_by` e `updated_by` no seu model, preenchidas com o usuário autenticado no momento.

Ao usar a *trait* `SoftDeletes` do Laravel, uma coluna `deleted_by` também será tratada por este pacote.

## Instalação

Este pacote requer Laravel 9 ou superior executando em PHP 8.2 ou superior.

Ele pode ser instalado usando o Composer:

```
composer require kseven/userstamps
```

## Uso

Seu model precisará incluir colunas `created_by` e `updated_by`, com valor padrão `null`.

Se estiver usando a *trait* `SoftDeletes` do Laravel, também precisará da coluna `deleted_by`.

O tipo das colunas deve corresponder ao tipo da coluna de ID na tabela de usuários.

Você pode criar as colunas de userstamp com:

```php
$table->userstamps();
$table->userstampSoftDeletes();
```

Agora você pode usar a trait no seu model, e os userstamps serão mantidos automaticamente:

```php
use KSeven\Userstamps\Traits\Userstamps;

class User extends Model {

    use Userstamps;
}
```

Opcionalmente, se quiser sobrescrever os nomes das colunas `created_by`, `updated_by` ou `deleted_by`, você pode definir constantes na sua classe. 
Certifique-se de que os nomes batem com os usados na sua migration:

```php
use KSeven\Userstamps\Traits\Userstamps;

class User extends Model {

    use Userstamps;

    const CREATED_BY = 'alt_created_by';
    const UPDATED_BY = 'alt_updated_by';
    const DELETED_BY = 'alt_deleted_by';
}
```

Ao utilizar esta trait, relacionamentos auxiliares estarão disponíveis para recuperar o usuário que criou, atualizou e excluiu (ao usar `SoftDeletes`) o model:


```php
$model->creator; // usuário que criou o model
$model->editor; // usuário que atualizou por último o model
$model->destroyer; // usuário que excluiu o model
```

Methods are also available to temporarily stop the automatic maintaining of userstamps on your models:

```php
$model->stopUserstamping(); // para a atualização dos userstamps no model
$model->startUserstamping(); // retoma a atualização dos userstamps no model
```

## Resolvendo Usuários

Por padrão, os usuários são resolvidos usando o método `Auth::id()` do Laravel, retornando o ID do usuário autenticado no momento.

Casos de uso mais avançados são suportados com uma estratégia de resolução personalizada.

Neste exemplo, é usado um método personalizado para obter o ID:

```php
use KSeven\Userstamps\Userstamps;

Userstamps::resolveUsing(
    fn () => auth()->user()->customUserIdResolutionMethod()
);
```

O método `Userstamps::resolveUsing` é ideal para ser utilizado no método `boot` do `AppServiceProvider`.

## Soluções Alternativas

Este pacote funciona conectando-se aos eventos de modelo do Eloquent, estando sujeito às mesmas limitações desses listeners.

Se você fizer alterações que ignoram o Eloquent, os eventos não serão disparados e os userstamps não serão atualizados.

Isso geralmente acontece ao realizar atualizações ou exclusões em massa nos modelos ou em seus relacionamentos.

Neste exemplo, os relacionamentos são atualizados via Eloquent e os userstamps **serão** mantidos:

```php
$model->foos->each(function ($item) {
    $item->bar = 'x';
    $item->save();
});
```

Neste exemplo, os relacionamentos são atualizados em massa, ignorando o Eloquent. 
Os userstamps **não serão** mantidos:

```php
$model->foos()->update([
    'bar' => 'x',
]);
```

Como alternativa, dois métodos auxiliares estão disponíveis — `updateWithUserstamps` e `deleteWithUserstamps`. 
Eles funcionam como update e delete, mas garantem que as colunas `updated_by` e `deleted_by` sejam atualizadas no modelo.

Você geralmente não precisará usar esses métodos, a não ser que esteja fazendo atualizações em massa que ignoram os eventos do Eloquent.

Neste exemplo, os modelos são atualizados em massa e os userstamps **não serão** mantidos:

```php
$model->where('name', 'foo')->update([
    'name' => 'bar',
]);
```

Neste exemplo, os modelos são atualizados em massa com o método auxiliar e os userstamps **serão** mantidos:

```php
$model->where('name', 'foo')->updateWithUserstamps([
    'name' => 'bar',
]);
```

## Licença

Este software de código aberto está licenciado sob a [MIT Licença](https://opensource.org/licenses/MIT).
