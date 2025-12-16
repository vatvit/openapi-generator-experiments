# PetStoreClient\PublicApi

All URIs are relative to https://petstore.swagger.io/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**findPetById()**](PublicApi.md#findPetById) | **GET** /pets/{id} |  |


## `findPetById()`

```php
findPetById($id): \PetStoreClient\Model\Pet
```



Returns a user based on a single ID, if the user does not have access to the pet

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\PublicApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 56; // int | ID of pet to fetch

try {
    $result = $apiInstance->findPetById($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PublicApi->findPetById: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **id** | **int**| ID of pet to fetch | |

### Return type

[**\PetStoreClient\Model\Pet**](../Model/Pet.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
