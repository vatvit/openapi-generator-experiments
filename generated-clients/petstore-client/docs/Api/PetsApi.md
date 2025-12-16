# PetStoreClient\PetsApi

All URIs are relative to https://petstore.swagger.io/v2, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**addPet()**](PetsApi.md#addPet) | **POST** /pets |  |
| [**deletePet()**](PetsApi.md#deletePet) | **DELETE** /pets/{id} |  |
| [**findPetById()**](PetsApi.md#findPetById) | **GET** /pets/{id} |  |
| [**findPets()**](PetsApi.md#findPets) | **GET** /pets |  |


## `addPet()`

```php
addPet($new_pet): \PetStoreClient\Model\Pet
```



Creates a new pet in the store. Duplicates are allowed

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\PetsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$new_pet = new \PetStoreClient\Model\NewPet(); // \PetStoreClient\Model\NewPet | Pet to add to the store

try {
    $result = $apiInstance->addPet($new_pet);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PetsApi->addPet: ', $e->getMessage(), PHP_EOL;
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



$apiInstance = new PetStoreClient\Api\PetsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 56; // int | ID of pet to delete

try {
    $apiInstance->deletePet($id);
} catch (Exception $e) {
    echo 'Exception when calling PetsApi->deletePet: ', $e->getMessage(), PHP_EOL;
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

## `findPetById()`

```php
findPetById($id): \PetStoreClient\Model\Pet
```



Returns a user based on a single ID, if the user does not have access to the pet

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\PetsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$id = 56; // int | ID of pet to fetch

try {
    $result = $apiInstance->findPetById($id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PetsApi->findPetById: ', $e->getMessage(), PHP_EOL;
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

## `findPets()`

```php
findPets($tags, $limit): \PetStoreClient\Model\Pet[]
```



Returns all pets from the system that the user has access to Nam sed condimentum est. Maecenas tempor sagittis sapien, nec rhoncus sem sagittis sit amet. Aenean at gravida augue, ac iaculis sem. Curabitur odio lorem, ornare eget elementum nec, cursus id lectus. Duis mi turpis, pulvinar ac eros ac, tincidunt varius justo. In hac habitasse platea dictumst. Integer at adipiscing ante, a sagittis ligula. Aenean pharetra tempor ante molestie imperdiet. Vivamus id aliquam diam. Cras quis velit non tortor eleifend sagittis. Praesent at enim pharetra urna volutpat venenatis eget eget mauris. In eleifend fermentum facilisis. Praesent enim enim, gravida ac sodales sed, placerat id erat. Suspendisse lacus dolor, consectetur non augue vel, vehicula interdum libero. Morbi euismod sagittis libero sed lacinia.  Sed tempus felis lobortis leo pulvinar rutrum. Nam mattis velit nisl, eu condimentum ligula luctus nec. Phasellus semper velit eget aliquet faucibus. In a mattis elit. Phasellus vel urna viverra, condimentum lorem id, rhoncus nibh. Ut pellentesque posuere elementum. Sed a varius odio. Morbi rhoncus ligula libero, vel eleifend nunc tristique vitae. Fusce et sem dui. Aenean nec scelerisque tortor. Fusce malesuada accumsan magna vel tempus. Quisque mollis felis eu dolor tristique, sit amet auctor felis gravida. Sed libero lorem, molestie sed nisl in, accumsan tempor nisi. Fusce sollicitudin massa ut lacinia mattis. Sed vel eleifend lorem. Pellentesque vitae felis pretium, pulvinar elit eu, euismod sapien.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new PetStoreClient\Api\PetsApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$tags = array('tags_example'); // string[] | tags to filter by
$limit = 56; // int | maximum number of results to return

try {
    $result = $apiInstance->findPets($tags, $limit);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PetsApi->findPets: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **tags** | [**string[]**](../Model/string.md)| tags to filter by | [optional] |
| **limit** | **int**| maximum number of results to return | [optional] |

### Return type

[**\PetStoreClient\Model\Pet[]**](../Model/Pet.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
