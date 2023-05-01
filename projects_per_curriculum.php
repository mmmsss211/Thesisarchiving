<?php 
if(isset($_GET['id'])){

    $qry = $conn->query("SELECT * FROM curriculum_list where `status` = 1 and id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            if(!is_numeric($k)){
                $curriculum[$k] = $v;
            }
        }
    }else{
        echo "<script> alert('Unkown Course ID'); location.replace('./') </script>";
    }

}else{
    echo "<script> alert('Course ID is required'); location.replace('./') </script>";
}

?>
<div class="content py-2">
    <div class="col-12">
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-body rounded-0">
                <h2>Archive List of <?= isset($curriculum['name']) ? $curriculum['name'] : "" ?> </h2>
                <p><small><?= isset($curriculum['description']) ? $curriculum['description'] : "" ?></small></p>
                <hr class="bg-navy">
                <?php 
                $id = isset($_GET['id']) ? $_GET['id'] : '';
                $limit = 10;
                $page = isset($_GET['p'])? $_GET['p'] : 1; 
                $offset = 10 * ($page - 1);
                $paginate = " limit {$limit} offset {$offset}";
                $wherecid = " and curriculum_id = '{$id}' ";
                $students = $conn->query("SELECT * FROM `student_list` where id in (SELECT student_id FROM archive_list where `status` = 1 {$wherecid})");
                //$student_arr = array_column($students->fetch_all(MYSQLI_ASSOC),'email','id');
                while($row_students = $students->fetch_assoc()):
                $firstname = $row_students['firstname'];
                $lastname = $row_students['lastname'];
                $email = $row_students['email'];
                endwhile;
                $count_all = $conn->query("SELECT * FROM archive_list where `status` = 1 {$wherecid}")->num_rows;    
                $pages = ceil($count_all/$limit);
                $archives = $conn->query("SELECT * FROM archive_list where `status` = 1 {$wherecid} order by unix_timestamp(date_created) desc {$paginate}");    
                ?>
                <div class="list-group">
                    <?php 
                    while($row = $archives->fetch_assoc()):
                        $row['abstract'] = strip_tags(html_entity_decode($row['abstract']));
                    ?>
                    <a href="./?page=view_archive&id=<?= $row['id'] ?>" class="text-decoration-none text-dark list-group-item list-group-item-action">
                        <div class="row">
                            <div class="col-lg-4 col-md-5 col-sm-12 text-center">
                                <img src="<?= validate_image($row['banner_path']) ?>" class="banner-img img-fluid bg-gradient-dark" alt="Banner Image">
                            </div>
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <h3 class="text-navy"><b><?php echo $row['title'] ?></b></h3>
                                 <small class="text-muted">By <b class="text-info"><?= isset($row['student_id']) ? $firstname.' '.$lastname.'</b> <b>('.$email.')</b>' : "N/A" ?></small>
                                <p class="truncate-5"><?= $row['abstract'] ?></p>
                            </div>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="card-footer clearfix rounded-0">
                <div class="col-12">
                    <div class="row">
                       <div class="col-md-6"><span class="text-muted">Display <?php if($archives->num_rows <= 1) { echo 'Item: '; } else { echo 'Items: ';}  echo $archives->num_rows; ?></span></div>
                        <div class="col-md-6">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <li class="page-item"><a class="page-link" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $page - 1 ?>" <?= $page == 1 ? 'disabled' : '' ?>>«</a></li>
                                <?php for($i = 1; $i<= $pages; $i++): ?>
                                <li class="page-item"><a class="page-link <?= $page == $i ? 'active' : '' ?>" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $i ?>"><?= $i ?></a></li>
                                <?php endfor; ?>
                                <li class="page-item"><a class="page-link" href="./?page=projects_per_curriculum&id=<?= $id ?>&p=<?= $page + 1 ?>" <?= $page == $pages || $pages <= 1 ? 'disabled' : '' ?>>»</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>