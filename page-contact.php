<?php
// Template Name: Contact Page
get_header(); ?>

<main class="main-content">
  <!-- Contact Hero Section -->
  <section class="page-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 300;">CONTACT</h1>
      <p style="color: white; font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Get in touch to schedule your appointment or ask questions</p>
    </div>
  </section>

  <!-- Contact Information Section -->
  <section class="contact-info-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
      
      <!-- Contact Details -->
      <div class="contact-details" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h2 style="color: var(--red-accent); font-size: 2rem; margin-bottom: 30px;">Get In Touch</h2>
        
        <div class="contact-item" style="margin-bottom: 25px;">
          <h3 style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 10px;">📍 Address</h3>
          <p style="color: var(--text-dark); line-height: 1.6;">
            401 Meadowlands Dr<br>
            Hillsborough, NC 27278
          </p>
        </div>
        
        <div class="contact-item" style="margin-bottom: 25px;">
          <h3 style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 10px;">📞 Phone</h3>
          <p style="color: var(--text-dark);">
            <a href="tel:9192415092" style="color: var(--red-accent); text-decoration: none; font-size: 1.1rem; font-weight: 600;">(919) 241-5092</a>
          </p>
        </div>
        
        <div class="contact-item" style="margin-bottom: 25px;">
          <h3 style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 10px;">✉️ Email</h3>
          <p style="color: var(--text-dark);">
            <a href="mailto:info@youhealit.com" style="color: var(--red-accent); text-decoration: none;">info@youhealit.com</a>
          </p>
        </div>
        
        <div class="contact-item">
          <h3 style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 15px;">🕒 Hours</h3>
          <div style="color: var(--text-dark); line-height: 1.8;">
            <p style="margin: 5px 0;"><strong>Monday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
            <p style="margin: 5px 0;"><strong>Tuesday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
            <p style="margin: 5px 0;"><strong>Wednesday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
            <p style="margin: 5px 0;"><strong>Thursday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
            <p style="margin: 5px 0;"><strong>Friday:</strong> 9:00 – 1:00</p>
            <p style="margin: 5px 0;"><strong>Saturday:</strong> Closed</p>
            <p style="margin: 5px 0;"><strong>Sunday:</strong> 10:00 – 1:00 (By Appointment)</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Map Section -->
  <?php get_template_part('template-parts/hch-map'); ?>

  <!-- Emergency Info Section -->
  <section class="emergency-info" style="background: var(--red-accent); color: white; padding: 60px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="font-size: 2rem; margin-bottom: 20px; font-weight: 300;">Need Immediate Care?</h2>
      <p style="font-size: 1.1rem; margin-bottom: 30px; line-height: 1.6;">
        For urgent health concerns or to schedule a same-day appointment, please call us directly at 
        <a href="tel:9192415092" style="color: white; font-weight: bold; text-decoration: underline;">(919) 241-5092</a>
      </p>
      <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="tel:9192415092" class="btn" style="background: white; color: var(--red-accent); padding: 15px 25px;">Call Now</a>
        <a href="#" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 25px;">Request Appointment</a>
      </div>
    </div>
  </section>
</main>

<style>
.contact-details:hover,
.contact-form:hover {
  transform: translateY(-2px);
  transition: transform 0.3s ease;
}

@media (max-width: 768px) {
  .contact-info-section > div {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php get_footer(); ?>>
      
      <!-- Contact Form -->
      <div class="contact-form" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h2 style="color: var(--red-accent); font-size: 2rem; margin-bottom: 30px;">Send Us a Message</h2>
        
        <!-- Placeholder for Aloha contact form -->
        <div id="aloha-contact-form">
          <!-- Your Aloha contact form script will go here -->
          <form style="display: flex; flex-direction: column; gap: 20px;">
            <div>
              <label style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 600;">Name *</label>
              <input type="text" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
              <label style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 600;">Email *</label>
              <input type="email" required style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
              <label style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 600;">Phone</label>
              <input type="tel" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px;">
            </div>
            
            <div>
              <label style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 600;">Subject</label>
              <select style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option>General Inquiry</option>
                <option>New Patient</option>
                <option>Appointment Request</option>
                <option>Insurance Question</option>
                <option>Other</option>
              </select>
            </div>
            
            <div>
              <label style="display: block; margin-bottom: 5px; color: var(--text-dark); font-weight: 600;">Message *</label>
              <textarea required rows="5" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-red" style="align-self: flex-start; font-size: 1rem; padding: 15px 30px;">
              Send Message
            </button>
          </form>
        </div>
      </div