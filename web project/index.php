<?php
session_start();
require 'config.php';

$sql = "SELECT * FROM shoes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Shoe Store - Stylish Home</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;600&display=swap');

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0; padding: 0;
    font-family: 'Poppins', sans-serif;
    background: #1a1a2e;
    color: #fff;
  }

  header {
    background: linear-gradient(90deg, #16213e 0%, #0f3460 100%);
    padding: 30px 20px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  }

  header h1 {
    margin: 0;
    font-weight: 600;
    font-size: 2.8rem;
    letter-spacing: 2px;
    color: #e94560;
    text-transform: uppercase;
  }

  .shoe-container {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(260px,1fr));
    gap: 30px;
    padding: 40px 20px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .shoe-card {
    background: #0f3460;
    border-radius: 20px;
    box-shadow: 0 15px 25px rgba(233, 69, 96, 0.4);
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .shoe-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 25px 40px rgba(233, 69, 96, 0.7);
  }

  .shoe-card img {
    width: 100%;
    height: 220px;
    object-fit: contain;
    background: #16213e;
    border-bottom: 2px solid #e94560;
    transition: transform 0.4s ease;
  }

  .shoe-card:hover img {
    transform: scale(1.1);
  }

  .shoe-info {
    padding: 20px 25px;
    text-align: center;
  }

  .shoe-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 12px 0 6px;
    color: #f5f6f7;
  }

  .shoe-category {
    font-size: 0.9rem;
    color: #e94560;
    font-weight: 500;
    letter-spacing: 1px;
    margin-bottom: 15px;
    text-transform: uppercase;
  }

  .shoe-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #00ff99;
    margin-bottom: 20px;
  }

  form button {
    background: #e94560;
    border: none;
    padding: 14px 40px;
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 50px;
    cursor: pointer;
    transition: background 0.3s ease;
    box-shadow: 0 8px 15px rgba(233, 69, 96, 0.4);
  }

  form button:hover {
    background: #ff2e63;
    box-shadow: 0 12px 20px rgba(255, 46, 99, 0.7);
  }

  /* Responsive for smaller screens */
  @media(max-width: 600px) {
    .shoe-card img {
      height: 180px;
    }
  }
</style>
</head>
<body>

<header>
  <h1>Step Into Style - Online Shoe Store</h1>
</header>

<div class="shoe-container">
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="shoe-card">
      <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" />
      <div class="shoe-info">
        <div class="shoe-name"><?php echo htmlspecialchars($row['name']); ?></div>
        <div class="shoe-category"><?php echo htmlspecialchars($row['category']); ?></div>
        <div class="shoe-price">$<?php echo number_format($row['price'], 2); ?></div>
        <form method="post" action="add_to_cart.php">
          <input type="hidden" name="shoe_id" value="<?php echo $row['id']; ?>" />
          <button type="submit">Add to Cart</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
