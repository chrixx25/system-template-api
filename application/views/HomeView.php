<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>

     <!-- Bootstrap CSS -->
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

     <link rel="stylesheet" href="<?php echo base_url('assets/css/home.css') ?>">
</head>
<body>
    <div class='table-container'>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Middle Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Company</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Updated At</th>
                    <th scope=""></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- JQUERY -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(() => {
            const base_url = '<?php echo base_url() ?>';
            let access_token = localStorage.getItem('token_rest')
            const token = access_token ? JSON.parse(access_token).token : undefined;

            const checkSession = () => {
                if( !token ) location.replace('http://192.168.1.251/rest_project/login')
            }
            checkSession();

            const getAllUsers = () => {
                $.ajax({
                    url: `${base_url}/users`,
                    // method: "POST", //default is GET
                    // data: { id : menuId },
                    headers: {
                        // Accept: 'application/json',
                        Authorization: token
                    },
                    dataType: "json",
                    success: ({data,status}) => {
                        
                        console.log(data)
                        let list = '<tr><td colspan=8>No record</td></tr>';
                        if( data.length > 0 ) {
                            list = '';
                            data.map(({ID,FirstName,MiddleName,LastName,GenderID,Company,CreatedDate,ModifiedDate})=>{
                                list += `<tr>
                                        <td>${ID}</td>
                                        <td>${FirstName}</td>
                                        <td>${MiddleName}</td>
                                        <td>${LastName}</td>
                                        <td>${GenderID}</td>
                                        <td>${Company}</td>
                                        <td>${CreatedDate}</td>
                                        <td>${ModifiedDate}</td>
                                        <td>
                                            <button type="button" data-id=${ID} class="btn btn-danger">del</button>
                                            <button type="button" data-id=${ID} class="btn btn-info">edit</button>
                                        </td>
                                    </tr>`
                            })
                        }
                        
                        $('.table tbody').html(list)
                    },
                    error: (err) => {
                        const {status,statusText} = err
                        console.log({err,status,statusText})
                        // alert(`${status}: ${statusText}`)
                        alert(`${statusText}: ${err.responseJSON.error}`)
                    }
                });
            }

            getAllUsers();
        })
    </script>
</body>
</html>