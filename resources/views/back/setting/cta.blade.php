@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class=" mb-0"><b>{{ __('Call to Action Settings') }}</b></h3>
            </div>
        </div>
    </div>

	<!-- Settings Form -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			
			<form action="{{ route('back.setting.cta.update') }}" method="POST">
				@csrf
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cta_enabled" class="form-label">{{ __('Enable Call to Action') }}</label>
							<div class="switch switch-bootstrap status radio-check">
								<input type="checkbox" class="switch switch-bootstrap status radio-check" 
									   name="cta_enabled" id="cta_enabled" value="1" 
									   {{ old('cta_enabled', $setting->cta_enabled ?? 0) ? 'checked' : '' }}>
								<label for="cta_enabled" class="switch-label"></label>
							</div>
							<small class="form-text text-muted">{{ __('Show contact buttons on product pages') }}</small>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="cta_phone" class="form-label">{{ __('Phone Number') }} *</label>
							<input type="text" class="form-control" id="cta_phone" name="cta_phone" 
								   value="{{ old('cta_phone', $setting->cta_phone ?? '01872200587') }}" 
								   placeholder="01872200587" required>
							<small class="form-text text-muted">{{ __('This number will be displayed and used for calls') }}</small>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="cta_whatsapp" class="form-label">{{ __('WhatsApp Number') }}</label>
							<input type="text" class="form-control" id="cta_whatsapp" name="cta_whatsapp" 
								   value="{{ old('cta_whatsapp', $setting->cta_whatsapp ?? '') }}" 
								   placeholder="01872200587">
							<small class="form-text text-muted">{{ __('Leave empty to use phone number for WhatsApp') }}</small>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="cta_text" class="form-label">{{ __('Call to Action Text') }}</label>
							<input type="text" class="form-control" id="cta_text" name="cta_text" 
								   value="{{ old('cta_text', $setting->cta_text ?? 'For order call us or chat on WhatsApp') }}" 
								   placeholder="For order call us or chat on WhatsApp">
							<small class="form-text text-muted">{{ __('This text will appear below the price on product pages') }}</small>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h5>{{ __('Preview') }}</h5>
							</div>
							<div class="card-body">
								{{-- Call to Action Text Preview --}}
								<p class="text-muted mb-3" id="cta-text-preview">{{ old('cta_text', $setting->cta_text ?? 'For order call us or chat on WhatsApp') }}</p>
								
								{{-- Contact Section Preview --}}
								<div class="contact-section-preview d-flex align-items-center gap-3 flex-wrap p-3">
									
									{{-- Phone Number Preview --}}
									<div class="phone-number-section">
										<a href="#" class="phone-number-link">
											<span class="phone-icon" style="font-size: 24px; animation: bounce 2s infinite;">📞</span>
											<span class="phone-number-blinking" style="font-size: 20px; font-weight: bold; color: #28a745; animation: blink 1.5s infinite;">{{ old('cta_phone', $setting->cta_phone ?? '01872200587') }}</span>
										</a>
									</div>
									
									{{-- WhatsApp Image Preview --}}
									<div class="whatsapp-section">
										<a href="#" class="whatsapp-image-link">
											<img src="{{ asset('assets/images/whatsapp-click-to-chat.png') }}" 
												 alt="WhatsApp Chat" 
												 style="max-height: 50px; width: auto; border: none; box-shadow: none; background: none;">
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="form-group mt-4">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-save"></i> {{ __('Update Settings') }}
					</button>
				</div>
			</form>
		</div>
	</div>

</div>
<!-- End of Main Content -->

@endsection

@section('scripts')
<style>
/* Preview Animations */
@keyframes blink {
    0%, 50% {
        opacity: 1;
        text-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
    }
    51%, 100% {
        opacity: 0.3;
        text-shadow: 0 0 2px rgba(40, 167, 69, 0.1);
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-5px);
    }
    60% {
        transform: translateY(-3px);
    }
}

.contact-section-preview .phone-number-link {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #333;
}

.contact-section-preview .whatsapp-image-link {
    display: block;
}
</style>

<script>
$(document).ready(function() {
    // Update preview when phone number changes
    $('#cta_phone').on('input', function() {
        $('.phone-number-blinking').text($(this).val() || '01872200587');
    });
    
    // Update preview when WhatsApp number changes
    $('#cta_whatsapp').on('input', function() {
        if ($(this).val()) {
            // Update WhatsApp link preview if needed
        }
    });
    
    // Update preview when CTA text changes
    $('#cta_text').on('input', function() {
        $('#cta-text-preview').text($(this).val() || 'For order call us or chat on WhatsApp');
    });
});
</script>
@endsection
