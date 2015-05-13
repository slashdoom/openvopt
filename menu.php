<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php">OpenVoPT</a>
    </div>
    <div>
      <ul class="nav navbar-nav">
        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
        Phones<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="p_in_cm.php">Phones in Call Managers</a></li>
            <li><a href="p_on_sw.php">Phones found on Switches</a></li>
            <li><a href="p_not_on_sw.php">Phones not found on Switches</a></li>
          </ul>
        </li>
        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">
        Settings<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="s_cm.php">Call Managers</a></li>
            <li><a href="s_sw.php">Switches</a></li>
          </ul>
        </li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['user']; ?></a></li>
        <li><a href="login.php?out=<?php echo $_SESSION['user']; ?>"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
