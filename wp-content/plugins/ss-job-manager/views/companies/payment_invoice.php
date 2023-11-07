<section class="section" id="section_2107755050">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-1812075560">
      <div id="col-1738432856" class="col small-12 large-12">
        <div class="col-inner">
          <div id="text-3083575005" class="text">
            <h2 style="margin-bottom: 0;">Payment Invoice</h2>
            <style>
              #text-3083575005 {
                font-size: 1.2rem;
                color: rgb(0, 0, 0);
              }

              #text-3083575005>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
        </div>
        <style>
          #col-1738432856>.col-inner {
            margin: 0px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-1738432856>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-742262325" class="col small-12 large-12">
        <div class="col-inner" style="background-color:rgb(255,255,255);">
          <table class="payment-invoice-table">
            <thead>
              <tr>
                <th>Status</th>
                <th>Invoice code</th>
                <th>Position</th>
                <th>Company name</th>
                <th>Payment due</th>
                <th>Total</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
                <?php foreach($invoice_jobs as $invoice_job):?>
                    <?php 
                    $invoice_status = get_post_meta($invoice_job->ID, 'invoice_status', true);
                    $invoice_code = get_post_meta($invoice_job->ID, 'invoice_code', true);
                    $payment_due = get_field('payment_due', $invoice_job->ID);
                    $invoice_total = get_post_meta($invoice_job->ID, 'invoice_total', true);
                    ?>
                    <tr class="single-invoice" data-job="<?php echo $invoice_job->ID?>">
                        <?php echo $invoice_status == 'paid' ? '<td>Paid</td>' : '<td>Unpaid</td>'?>             
                        <td><?php echo $invoice_code?></td>
                        <td><?php echo $invoice_job->post_title?></td>
                        <td><?php echo get_the_title($company_id)?></td>
                        <td><?php echo $payment_due?></td>
                        <td>$<?php echo $invoice_total?></td>
                        <td>
                        <!-- <a class="button primary" style="border-radius:10px">
                            <span>Download</span>
                        </a> -->
                        <a class="button success view-invoice-btn" style="border-radius:10px">
                            <span>View</span>
                        </a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <style>
          #col-742262325>.col-inner {
            padding: 25px 15px 15px 15px;
            border-radius: 15px;
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_2107755050 {
      padding-top: 30px;
      padding-bottom: 30px;
      background-color: rgb(232, 245, 255);
    }

    #section_2107755050 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_2107755050 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }
  </style>
</section>
<div class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close">&times;</span>
        <div class="popup-invoice">
            
        </div>
  </div>
</div>