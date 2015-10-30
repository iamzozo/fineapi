# fineapi
A simple resource framework for WordPress.

The plugin handles three types of request: GET, PUT, POST
Your requests are mapped to a given **controller** and to a **method** in your controller.

For example:
`api/posts/1` with PUT request will call **posts** controller and the **update** method, with **1** parameter.

# Controllers
Each resource are different controllers, ex.: `posts.php`. These controllers should be stored in the plugin folder's **controller** directory.
Your requests will be checked if the controller exists.

# Methods
Five methods available in the controller, what gets called:
- **index** - GET request without parameter will call this, for example `api/posts`
- **show** - GET request with a parameter `api/posts/1`
- **store** - POST request `api/posts`
- **update** - PUT request `api/posts/1`
- **destroy** - DELETE request `api/posts/1`

# Example

1. A POST request send to `api/posts` with its post data.
2. The plugin will call **posts** controller, and the **store** method.
3. In your posts controller

```
function store() {
    global $fineapi;
    $id = wp_insert_post([
        'post_title' => 'Testing',
        'post_content' => 'Hello world!',
    ]);
    foreach($_FILES as $file) {
        $fineapi->upload($file, [
            'post_parent' => $id
        ]);
    }
}
```

This will create a post, and upload files for it.
