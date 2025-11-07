<template>
  <div class="settings-container">
    <div class="settings-header">
      <h1>⚙️ 系统设置</h1>
      <p class="subtitle">配置应用的各项参数</p>
    </div>

    <div class="settings-content">
      <div class="setting-card">
        <div class="setting-info">
          <h3>⭐ 每次加星星上限</h3>
          <p class="description">设置一次性可以添加的最大星星数量</p>
        </div>
        <div class="setting-control">
          <input
            type="number"
            v-model.number="maxStarsPerAdd"
            class="setting-input"
            min="1"
            max="999"
            @blur="handleSave"
          />
          <span class="unit">颗</span>
        </div>
      </div>

      <div v-if="saveMessage" class="save-message" :class="saveMessageType">
        {{ saveMessage }}
      </div>

      <div class="setting-actions">
        <button class="btn-save" @click="handleSave" :disabled="saving">
          {{ saving ? '保存中...' : '💾 保存设置' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { settingsApi } from '@/api/settings';

const maxStarsPerAdd = ref(100);
const saving = ref(false);
const saveMessage = ref('');
const saveMessageType = ref<'success' | 'error'>('success');

const loadSettings = async () => {
  try {
    const value = await settingsApi.get('max_stars_per_add');
    maxStarsPerAdd.value = value;
  } catch (error) {
    console.error('Failed to load settings:', error);
    saveMessage.value = '加载设置失败';
    saveMessageType.value = 'error';
  }
};

const handleSave = async () => {
  if (saving.value) return;

  // Validate
  if (maxStarsPerAdd.value < 1) {
    saveMessage.value = '数值必须大于 0';
    saveMessageType.value = 'error';
    return;
  }

  try {
    saving.value = true;
    saveMessage.value = '';

    await settingsApi.update('max_stars_per_add', maxStarsPerAdd.value);

    saveMessage.value = '✓ 设置已保存';
    saveMessageType.value = 'success';

    // Clear message after 3 seconds
    setTimeout(() => {
      saveMessage.value = '';
    }, 3000);
  } catch (error: any) {
    saveMessage.value = error.response?.data?.message || '保存失败';
    saveMessageType.value = 'error';
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.settings-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 20px;
}

.settings-header {
  text-align: center;
  margin-bottom: 40px;
}

.settings-header h1 {
  font-size: 32px;
  font-weight: bold;
  color: #333;
  margin: 0 0 8px 0;
}

.subtitle {
  font-size: 16px;
  color: #666;
  margin: 0;
}

.settings-content {
  background: white;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.setting-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 16px;
  margin-bottom: 24px;
}

.setting-info h3 {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin: 0 0 8px 0;
}

.description {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.setting-control {
  display: flex;
  align-items: center;
  gap: 12px;
}

.setting-input {
  width: 120px;
  height: 56px;
  font-size: 32px;
  font-weight: bold;
  text-align: center;
  border: 3px solid #84fab0;
  border-radius: 12px;
  color: #4caf50;
  transition: all 0.2s;
}

.setting-input:focus {
  outline: none;
  box-shadow: 0 0 0 4px rgba(132, 250, 176, 0.2);
  transform: scale(1.05);
}

.unit {
  font-size: 18px;
  font-weight: 600;
  color: #666;
}

.save-message {
  padding: 16px;
  border-radius: 12px;
  text-align: center;
  font-weight: 600;
  margin-bottom: 24px;
  animation: slideIn 0.3s ease;
}

.save-message.success {
  background: #e8f5e9;
  color: #2e7d32;
}

.save-message.error {
  background: #ffebee;
  color: #c62828;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.setting-actions {
  display: flex;
  justify-content: center;
}

.btn-save {
  padding: 16px 48px;
  font-size: 18px;
  font-weight: 600;
  border: none;
  border-radius: 16px;
  background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(132, 250, 176, 0.4);
  transition: all 0.2s;
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(132, 250, 176, 0.5);
}

.btn-save:active:not(:disabled) {
  transform: translateY(0);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .settings-container {
    padding: 20px 16px;
  }

  .settings-header h1 {
    font-size: 24px;
  }

  .settings-content {
    padding: 20px;
  }

  .setting-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
  }

  .setting-control {
    width: 100%;
    justify-content: center;
  }
}
</style>
