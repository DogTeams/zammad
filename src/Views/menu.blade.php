<form action="{{route('createTicket')}}" method="POST">
    @csrf
    <label for="name">Titre : </label>
    <input type="text" name="title" id="title" required>
    <label for="customer">Client : </label>
    <input type="text" name="customer" id="customer" required>
    <label for="group">ID de Groupe : </label>
    <input type="text" name="group" id="group">
    <label for="subject">Objet : </label>
    <input type="text" name="subject" id="subject" required>
    <label for="body">Contenu : </label>
    <input type="textarea" name="body" id="body">
    <label for="cc">Copie à : </label>
    <input type="text" name="cc" id="cc" required>
    <label for="to">A : </label>
    <input type="text" name="to" id="to" required>
    <label for="from">De : </label>
    <input type="text" name="from" id="from" required>
    <label for="type">Type : </label>
    <input type="text" name="type" id="type" required>
    <label for=""></label>
    <input type="radio" name="typeCreate" id="typeCreate" value="ticket" checked> Ticket
    <input type="submit" value="Créer un ticket">
</form><br>

<form action="" method="POST">
    @csrf
    <label for="rechercher">Rechercher par mot-clé :</label>
    <input type="text" name="search" id="search" required>
    <label for="type">Type :</label>
    <input type="text" name="type" id="type" required>
    <input type="submit" value="Rechercher">

</form><br>

<form action="" method="POST">
    @csrf
    <label for="rechercher">Rechercher par id :</label>
    <input type="text" name="find" id="find" required>
    <label for="type">Type :</label>
    <input type="text" name="type" id="type" required>
    <input type="submit" value="Rechercher">
</form><br>

<form action="" method="POST">
    @csrf
</form>