<?php session_start();
    $dsn = "mysql:host=localhost:3306;dbname=todo";
    $username = "root";
    $password = "";

try{
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e){
    echo "ERROR";
    echo $e->getMessage();
    die();
}

/*function changeToDo(int $id){
    global $todoList, $pdo;
    foreach($todoList as $todo){
        $check=($_POST["todo_".$todo['id']]==''?0:1);
        if ($check != $todo['done']){
            echo "HERE";
            $queryString = "UPDATE todolist SET done=".!$todo['done']." WHERE id=".$todo['id'];
            $query = $pdo->prepare($queryString);
            $query->execute();
        }
        
    }
    header("Location:./index.php");
}*/
?>

<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./stylesheet.css">
    <title>ToDoo - Home</title>
</head>

<body>
    <div>
        
    <form method="post" action="">
        
        <?php
            if ($_SESSION['name'] != ''){
                $query = 'SELECT * FROM todolist';
                $results = $pdo->prepare($query);
                $results->execute();

                $todoList=$results->fetchAll();
                if ($_POST['newToDo']!=''){
                                    
                    $content = $_POST['newToDo'];
                    ?>
                    <br>
                    
                    <?php 

                    $queryString = "INSERT INTO todolist (content, id_user) VALUES (:content, :id_user)";
                    
                    $data = [
                        'content' => $content,
                        'id_user' => $_SESSION['id'],
                    ];

                    $query = $pdo->prepare($queryString);
                    $query->execute($data);
                }else if(isset($_POST['delete'])){
                    $queryString = "DELETE FROM todolist WHERE id=".$_POST['delete'];
                    $query = $pdo->prepare($queryString);
                    $query->execute();
                
                }else if($_SESSION['admin']){
                    foreach($todoList as $todo){
                        $check=($_POST["todo_".$todo['id']]==''?0:1);
                        if ($check != $todo['done']){
                            $queryString = "UPDATE todolist SET done=".($todo['done']==1?0:1)." WHERE id=".$todo['id'];
                            $query = $pdo->prepare($queryString);
                            $query->execute();
                        }
                    
                    }
                }else {
                    foreach($todoList as $todo){
                        //print_r($_POST['edit_'.$todo['id']]."--------------");
            
                        if(isset($_POST['send_'.$todo['id']]) && isset($_POST['text_'.$todo['id']])){
                            $modif=$_POST['text_'.$todo['id']];
                            echo $modif;
                            if($_POST['text_'.$todo['id']] != $todo['content']){
                                $queryString = 'UPDATE todolist SET content="'.$modif.'" WHERE id='.$todo['id'];
                                var_dump($_POST['text_'.$todo['id']]);
                                $query = $pdo->prepare($queryString);
                                $query->execute();

                            }
                            
                        }else{
                            $check=($_POST["todo_".$todo['id']]==''?0:1);
                            if ($check != $todo['done']){
                                $queryString = "UPDATE todolist SET done=".($todo['done']==1?0:1)." WHERE id=".$todo['id'];
                                $query = $pdo->prepare($queryString);
                                $query->execute();
                            }
                        }
                        
                    
                    }
                }?>

                <h2>You are Connected ! HEllo <strong><?= $_SESSION['name'] ?></strong> !</h2>
                <input type="button" onclick="document.location='./logout.php'" value="Logout"/>  
                <H1>Your toDo List:</H1>

                
                <div>
                    <input type="text"  minlength="10" maxlength="255" size="100" id="newToDo" name="newToDo" placeholder="New toDo ...">                    
                    <button type=submit name="add">add</button>
                </div>
                <?php
                $query = 'SELECT * FROM todolist WHERE id_user='.$_SESSION['id'];
                $results = $pdo->prepare($query);
                $results->execute();

                $todoList=$results->fetchAll();

                if (count($todoList)>0){
                    foreach($todoList as $todo){
                        ?>
                            <div>
                                    <input type="checkbox" onChange="submit();" name="todo_<?=$todo['id']?>"  <?php echo ($todo['done']==1 ? 'checked' : '');?>>

                                    
                                    <input type="text" id="text_<?=$todo['id']?>" name="text_<?=$todo['id']?>" value=<?=$todo['content']?> readonly>
                                    
                                    <button name="delete" value="<?=$todo['id']?>" onClick="submit();" >DELETE</button>
                                    <button type="button" name="edit_"+<?=$todo['id']?> id="edit_<?=$todo['id']?>" me=<?=$todo['id']?>  onClick=newEdit(<?=$todo['id']?>) value="<?=$todo['id']?>">Edit</button>
                                    
                                    <button id="send_<?=$todo['id']?>" name="send_<?=$todo['id']?>" value="<?=$todo['id']?>" onClick="submit()"   style="display:none">SEND </button>
                            </div>
                        <?php
                    }
                    ?>
                    
                <?php    
                }
                if ($_SESSION['admin']){
                    ?>
                    <hr style="margin-top:5%"/>
                    <?php   
                    $queryUser = 'SELECT * FROM user WHERE id !='.$_SESSION['id'];
                    $results = $pdo->prepare($queryUser);
                    $results->execute();

                    $allUsers=$results->fetchAll();

                    if (count($allUsers)>0){
                        ?>
                        <div style="margin-top:5%">    
                        <table  style="margin:auto ;">
                            <thead>
                                <th colspan=<?=count($allUsers)?>>
                                    <H1>All users toDoList:</H1>
                                </th>
                            </thead>

                            <tbody>
                                <tr>
                                <?php
                                    foreach($allUsers as $user){
                                        $queryTodo = 'SELECT * FROM todolist WHERE id_user=' . $user['id'];
                                        $results = $pdo->prepare($queryTodo);
                                        $results->execute();
                        
                                        $allTodo=$results->fetchAll();

                                        ?>
                                        
                                            <td>
                                            <table>
                                                <thead>
                                                    <tr><th><?=$user['userName']?></th></tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if(count($allTodo)>0){
                                                        foreach($allTodo as $todo){
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                <input type="checkbox" onChange="submit();" name="todo_<?=$todo['id']?>"  <?php echo ($todo['done']==1 ? 'checked' : '');?>>

                                                                <?=$todo['content']?>

                                                                <button name="delete" value="<?=$todo['id']?>" onClick="submit();" class="delTable">DELETE</button>
                                                                </td>
                                                            </tr>
                                                            
                                                        <?php 
                                                        }
                                                    }else{
                                                        ?>
                                                        <tr>
                                                            <td>No toDo </td>
                                                        </tr>
                                                    
                                                <?php
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                            </td>
                                        
                                        <?php            
                                    }
                                ?></tr>
                            </tbody>
                        </table>   
                        </div>     
                        <?php
                    }
                }

                
            }else{
            ?>
                <div>  
                <h1 class="Message">You are not connected</h1>
                <input  type="button" onclick="document.location='./register.php'" value="Register" class="lonelyBtn"/>  
                <input  type="button" onclick="document.location='./connection.php'" value="Login" class="lonelyBtn"/>
                </div>
            <?php }
        ?>
    </div>
    <script>

        function newEdit(id){
            console.log("here"); 
            let test = document.getElementById("edit_"+id);
            console.log("text_"+test.getAttribute("me"));
            let text = document.getElementById("text_"+test.getAttribute("me"));
            let send = document.getElementById("send_"+test.getAttribute("me"));
            send.style.display = "block";
            text.removeAttribute("readonly");
        }
    </script>
    </form>
    

</body>
</html>