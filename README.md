# Know Issues
- Validations on PUT routes not work as expected, seems that the validation populates the entity object before validade the request. So if a request set a not null field to null, an error is thrown. A possible workaround is set the fields on the entity to accept null `?string`, but that doesn't seem like good practice.  
