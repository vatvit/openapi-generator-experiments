# PetStoreClient\ManagementApi

All URIs are relative to https://petstore.swagger.io/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**addPet()**](ManagementApi.md#addPet) | **POST** /pets |  |
| [**deletePet()**](ManagementApi.md#deletePet) | **DELETE** /pets/{id} |  |


## `addPet()`

```php
addPet($new_pet): \PetStoreClient\Model\Pet
```



Creates a new pet in the store. Duplicates are allowed

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\ManagementApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$new_pet = new \PetStoreClient\Model\NewPet(); // \PetStoreClient\Model\NewPet | Pet to add to the store

try {
    $result = $apiInstance->addPet($new_pet);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ManagementApi->addPet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **new_pet** | [**\PetStoreClient\Model\NewPet**](../Model/NewPet.md)| Pet to add to the store | |

### Return type

[**\PetStoreClient\Model\Pet**](../Model/Pet.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `deletePet()`

```php
deletePet($id)
```



deletes a single pet based on the ID supplied

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\ManagementApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 56; // int | ID of pet to delete

try {
    $apiInstance->deletePet($id);
} catch (Exception $e) {
    echo 'Exception when calling ManagementApi->deletePet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **int**| ID of pet to delete | |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
