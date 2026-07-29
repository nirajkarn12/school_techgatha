<?php
require_once __DIR__ . '/inc/functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get Active Candidates
$stmt = $pdo->prepare("
    SELECT *
    FROM tbl_elections
    WHERE is_active = 1
    ORDER BY election_post, candidate_name
");
$stmt->execute();

$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

$groups = [];

foreach ($candidates as $row) {
    $groups[$row['election_post']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Election</title>
<link rel="stylesheet" href="assets/bootstrap.min.css">
<link rel="stylesheet" href="assets/all.min.css">
<style>

body{
    margin:0;
    background:#f3f5f9;
    font-family:Arial, Helvetica, sans-serif;
}

.container-fluid{
    max-width:1800px;
    padding:25px;
}

/* Page Title */

.page-title{
    text-align:center;
    font-size:38px;
    font-weight:700;
    color:#1b1b1b;
    margin-bottom:35px;
    letter-spacing:.5px;
}

/* ==========================
   Ribbon Section Heading
========================== */

.section-title{
    position:relative;
    text-align:center;
    margin:45px 0 30px;
}

.section-title:before{
    content:"";
    position:absolute;
    left:0;
    right:0;
    top:50%;
    transform:translateY(-50%);
    height:2px;
    background:#d9e4f5;
    z-index:1;
}

.section-title span{
    display:inline-block;
    position:relative;
    z-index:2;
    background:linear-gradient(135deg,#0d6efd,#084298);
    color:#fff;
    font-size:18px;
    font-weight:700;
    padding:8px 38px;
    border-radius:4px;
    letter-spacing:.5px;
    box-shadow:0 6px 15px rgba(13,110,253,.25);
}

.section-title span:before{
    content:"";
    position:absolute;
    left:-18px;
    top:0;
    width:0;
    height:0;
    border-top:20px solid transparent;
    border-bottom:20px solid transparent;
    border-right:18px solid #0b5ed7;
}

.section-title span:after{
    content:"";
    position:absolute;
    right:-18px;
    top:0;
    width:0;
    height:0;
    border-top:20px solid transparent;
    border-bottom:20px solid transparent;
    border-left:18px solid #0b5ed7;
}

/* ==========================
   Candidate Card
========================== */

.election-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    background:#fff;
    transition:all .35s ease;
    box-shadow:0 5px 18px rgba(0,0,0,.08);
    height:100%;
}

.election-card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 35px rgba(0,0,0,.18);
}

.election-card img{
    width:100%;
    height:190px;
    object-fit:cover;
}

.card-body{
    padding:15px;
}

.card-body h6{
    font-size:15px;
    font-weight:700;
    margin-bottom:10px;
    color:#222;
    min-height:40px;
}

.badge-post{
    display:inline-block;
    background:#eef4ff;
    color:#0d6efd;
    border:1px solid #d3e3ff;
    padding:6px 14px;
    border-radius:50px;
    font-size:11px;
    font-weight:600;
    margin-bottom:15px;
}

/* Vote Button */

.vote-btn{
    border-radius:10px;
    font-size:13px;
    padding:9px;
    font-weight:600;
    transition:.3s;
}

.vote-btn:hover{
    transform:scale(1.03);
}

/* Refresh Button */

.refresh-btn{
    padding:12px 35px;
    border-radius:50px;
    font-weight:600;
}

/* Card Animation */

.election-card{
    animation:fadeUp .5s ease;
}

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

/* Responsive */

@media (max-width:1200px){

.election-card img{
    height:180px;
}

}

@media (max-width:992px){

.page-title{
    font-size:32px;
}

.section-title span{
    font-size:17px;
    padding:8px 30px;
}

.election-card img{
    height:170px;
}

}

@media (max-width:768px){

.container-fluid{
    padding:15px;
}

.page-title{
    font-size:26px;
    margin-bottom:25px;
}

.section-title{
    margin:35px 0 20px;
}

.section-title span{
    font-size:15px;
    padding:7px 24px;
}

.section-title span:before,
.section-title span:after{
    border-top:17px solid transparent;
    border-bottom:17px solid transparent;
}

.section-title span:before{
    left:-15px;
    border-right:15px solid #0b5ed7;
}

.section-title span:after{
    right:-15px;
    border-left:15px solid #0b5ed7;
}

.election-card img{
    height:145px;
}

.card-body{
    padding:12px;
}

.card-body h6{
    font-size:13px;
    min-height:auto;
}

.vote-btn{
    font-size:12px;
    padding:8px;
}

.badge-post{
    font-size:10px;
    padding:5px 12px;
}

.refresh-btn{
    width:100%;
}

}

</style>

</head>
<body>

<div class="container-fluid">

<h1 class="page-title">
  विद्यार्थी परिषद् निर्वाचन
</h1>

<?php foreach($groups as $post => $list){ ?>

<div class="section-title">
   <span><?php echo htmlspecialchars($post); ?></span>
</div>

<div class="row g-3">

<?php foreach($list as $candidate){ ?>

<div class="col-6 col-sm-4 col-md-3 col-lg-2">

<div class="card election-card">

<img
src="<?php echo BASE_URL; ?>assets/uploads/<?php echo htmlspecialchars($candidate['candidate_image']); ?>"
alt="<?php echo htmlspecialchars($candidate['candidate_name']); ?>">

<div class="card-body text-center">

<h6>
<?php echo htmlspecialchars($candidate['candidate_name']); ?>
</h6>

<div class="badge-post">
<?php echo htmlspecialchars($candidate['election_post']); ?>
</div>

<form
class="vote-form"
data-post="<?php echo htmlspecialchars($candidate['election_post']); ?>">

<input
type="hidden"
name="candidate"
value="<?php echo (int)$candidate['id']; ?>">

<button
type="submit"
class="btn btn-primary btn-sm w-100 vote-btn">
<i class="fa-solid fa-check"></i>
मतदान गर्नुहोस्
</button>

</form>

</div>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

<div class="text-center mt-5">

<button
class="btn btn-outline-primary refresh-btn"
onclick="location.reload();">

<i class="fa-solid fa-rotate-right"></i>
पृष्ठ रिफ्रेस गर्नुहोस्

</button>

</div>

</div>

<script>
document.querySelectorAll('.vote-form').forEach(function(form){

    form.addEventListener('submit',function(e){

        e.preventDefault();

        let post=this.dataset.post;
        let formData=new FormData(this);

        fetch('vote.php',{
            method:'POST',
            body:formData
        })
        .then(response=>response.json())
        .then(function(data){

            if(data.success){

                document.querySelectorAll('.vote-form').forEach(function(f){

                    if(f.dataset.post===post){

                        let btn=f.querySelector('.vote-btn');

                        btn.disabled=true;
                        btn.innerHTML='<i class="fa-solid fa-check"></i> मतदान गरिसक्नुभयो';
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-secondary');

                    }

                });

            }else{

                alert(data.message || "मतदान गर्न सकिएन।");

            }

        })
        .catch(function(){

 alert("सर्भरमा समस्या आयो।");

        });

    });

});
</script>



<script src="assets/bootstrap.bundle.min.js"></script>

</body>
</html>