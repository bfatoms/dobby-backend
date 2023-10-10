for developers

If you want create a new permissions go to 
app/config/permissions.php and add a permission to the file lets say you wanted to add "ban-user" to organization_settings

"organization-settings" => [
    "index", "show", "create", "update", "trash", "restore", "force-delete", "ban-user"
],

add routes to app/routes/api.php
```
Route::post('users/{user}/ban', 'UserController@banUser');
```

then on the OrganizationPolicy add
```
public function banUser()
{
    return true;
}
```


then on the UserController add
```
public function banUser(User $user)
{
    $this->authorize('banUser', $user);
    $user->update(['banned' => true]);
    return $user->fresh();
}
```