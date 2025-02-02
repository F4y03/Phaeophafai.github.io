<?php
session_start();
include 'includes/header.php';
?>

<main style="max-width: 800px; margin: 40px auto; padding: 30px; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-radius: 12px;">
    <h2 style="color: #2c3e50; font-size: 28px; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 3px solid #3498db;">📬 ติดต่อเรา</h2>
    
    <form action="submit_contact.php" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Name Field -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="name" style="font-weight: 600; color: #34495e;">👤 ชื่อ:</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                required 
                style="padding: 12px 16px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: 16px; transition: all 0.3s;"
                onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
            >
        </div>

        <!-- Email Field -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="email" style="font-weight: 600; color: #34495e;">📧 อีเมล:</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                required 
                style="padding: 12px 16px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: 16px; transition: all 0.3s;"
                onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
            >
        </div>

        <!-- Message Field -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <label for="message" style="font-weight: 600; color: #34495e;">💬 ข้อความ:</label>
            <textarea 
                id="message" 
                name="message" 
                required 
                rows="5"
                style="padding: 12px 16px; border: 1px solid #bdc3c7; border-radius: 6px; font-size: 16px; resize: vertical; transition: all 0.3s;"
                onfocus="this.style.borderColor='#3498db'; this.style.boxShadow='0 0 0 3px rgba(52,152,219,0.1)'"
                onblur="this.style.borderColor='#bdc3c7'; this.style.boxShadow='none'"
            ></textarea>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            style="align-self: flex-start; background: #2ecc71; color: white; padding: 14px 30px; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; margin-top: 15px;"
            onmouseover="this.style.background='#27ae60'; this.style.transform='translateY(-2px)'"
            onmouseout="this.style.background='#2ecc71'; this.style.transform='none'"
        >
            📤 ส่งข้อความ
        </button>
    </form>

    <!-- Optional: Success/Error Message -->
    <?php if(isset($_SESSION['message'])): ?>
        <div style="margin-top: 25px; padding: 15px; background: <?= $_SESSION['message_type'] === 'error' ? '#f8d7da' : '#d4edda' ?>; color: <?= $_SESSION['message_type'] === 'error' ? '#721c24' : '#155724' ?>; border-radius: 6px; border: 1px solid <?= $_SESSION['message_type'] === 'error' ? '#f5c6cb' : '#c3e6cb' ?>;">
            <?= $_SESSION['message'] ?>
        </div>
    <?php 
        unset($_SESSION['message']);
        endif; 
    ?>
</main>

<?php include 'includes/footer.php'; ?>