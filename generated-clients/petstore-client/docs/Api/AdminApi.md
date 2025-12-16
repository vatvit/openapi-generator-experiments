# PetStoreClient\AdminApi

All URIs are relative to https://petstore.swagger.io/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**deletePet()**](AdminApi.md#deletePet) | **DELETE** /pets/{id} |  |


## `deletePet()`

```php
deletePet($id)
```



deletes a single pet based on the ID supplied

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\AdminApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 56; // int | ID of pet to delete

try {
    $apiInstance->deletePet($id);
} catch (Exception $e) {
    echo 'Exception when calling AdminApi->deletePet: ', $e->getMessage(), PHP_EOL;
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
