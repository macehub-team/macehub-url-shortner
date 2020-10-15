<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/docs/4.0/assets/img/favicons/favicon.ico">

    <title>Macehub URL Shortner</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/sticky-footer/">

    <!-- Bootstrap core CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sticky-footer.css" rel="stylesheet">

    <style type="text/css">
      form#logout{
        float: right;
        display: inline-block;
      }
    </style>
  </head>

  <body>

    <!-- Begin page content -->
    <main role="main" class="container">
      <h1 class="mt-5">
        Macehub URL Shortner
        <form id="logout" action="/" method="post"><input type="submit" name="logout" value="Logout" class="btn btn-danger"></form>
      </h1>
      <div class="row">
      	<div class="col-md-12">
		  <hr class="my-4">
		    <?php 
		      	if($success != null){
	      	?>
				<div class="alert alert-success" role="alert">
				  <?=$success?>
				</div>
			<?php
	      		}
		      	else if($error != null){
	      	?>
				<div class="alert alert-danger" role="alert">
				  <?=$error?>
				</div>
			<?php
	      		}
		    ?>


          <h4 class="mb-3">Short URL</h4>
          <form action="/" method="POST" >

            <div class="row">
              <div class="col-md-4 mb-3">
                <input type="text" class="form-control" name="slug" placeholder="Slug ( Eg: macehub )" value="" required>
              </div>
              <div class="col-md-4 mb-3">
                <input type="text" class="form-control" name="url" placeholder="Full URL ( Eg: https://www.macehub.in )" value="" required>
              </div>
              <div class="col-md-4 mb-3">
                <button class="btn btn-primary btn-block" type="submit">Short URL</button>
              </div>
          </form>


        </div>
       </div>
      </div>

      <div class="row mt-5">
      	<div class="col-md-12">
          <h4 class="mb-3">Shorten URLs</h4>
			<table class="table">
				<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col">Slug</th>
						<th scope="col">URL</th>
						<th scope="col">Created On</th>
            <th scope="col">Creator</th>
            <th scope="col">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($stmt->fetchAll() as $k=>$v) {
							?>
                <tr>
                  <td scope="row"><?=$v['id']?></td>
                  <td><span class="text-secondary">https://goto.macehub.in/</span><b class="text-info"><?=$v['slug']?><b></td>
                  <td><a href="<?=$v['url']?>" target="_blank"><?=$v['url']?></a></td>
                  <td><?=$v['created_at']?></td>
                  <td><?=$v['creator']?></td>
                  <td>
                    <form action="/" method="post" onsubmit="return confirm('Are you sure to delete');">
                      <input type="hidden" name="delete" value="<?=$v['id']?>">
                      <input type="submit" value="Delete">
                    </form>
                  </td>
                </tr>
              <?php
						}
					?>
				</tbody>
			</table>

      	</div>
      </div>                 
    </main>

  </body>
</html>
