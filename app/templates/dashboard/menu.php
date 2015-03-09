<div class="navbar-collapse collapse templatemo-sidebar">
        <ul class="templatemo-sidebar-menu">
          <li>
            <form class="navbar-form">
              <input type="text" class="form-control" id="templatemo_search_box" placeholder="Search...">
              <span class="btn btn-default">Go</span>
            </form>
          </li>
          <li class="active"><a href="#"><i class="fa fa-home"></i>Dashboard</a></li>
          <li class="sub">
            <a href="javascript:;">
              <i class="fa fa-database"></i> Nested Menu <div class="pull-right"><span class="caret"></span></div>
            </a>
            <ul class="templatemo-submenu">
              <li><a href="#">Aenean</a></li>
              <li><a href="#">Pellentesque</a></li>
              <li><a href="#">Congue</a></li>
              <li><a href="#">Interdum</a></li>
              <li><a href="#">Facilisi</a></li>
            </ul>
          </li>
          <li><a href="/ebay/list"><i class="fa fa-cubes"></i><span class="badge pull-right"><?php $m = new \models\ebay\item(); echo $m->countItems();?></span>Items</a></li>
          <!--<li><a href="#"><i class="fa fa-cubes"></i><span class="badge pull-right">9</span>Data Visualization</a></li>
          <li><a href="#"><i class="fa fa-map-marker"></i><span class="badge pull-right">42</span>Maps</a></li>-->
          <li><a href="/user/users/"><i class="fa fa-users"></i><span class="badge pull-right">NEW</span>Manage Users</a></li>
          <li><a href="#"><i class="fa fa-cog"></i>Preferences</a></li>
          <li><a href="javascript:;" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-sign-out"></i>Sign Out</a></li>
        </ul>
</div><!--/.navbar-collapse -->

