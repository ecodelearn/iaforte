# 🤖 IA FORTE - Blueprint do Site

> **Consultoria especializada em Inteligência Artificial para micro e pequenas empresas, com foco especial no setor jurídico.**

---

## 🎨 Sistema de Cores

### Tema Claro (Light Mode)
- **🔷 Primária**: `#1a5b5a` - Verde corporativo elegante
- **🔥 Accent**: `#ff5722` - Laranja vibrante para CTAs
- **⭐ Secundária**: `#ffc107` - Amarelo dourado para destaques
- **🌿 Terciária**: `#4caf50` - Verde natural para sucesso

### Tema Escuro (Dark Mode)
- **🔷 Primária**: `#4db6ac` - Verde água luminoso
- **🔥 Accent**: `#ff7043` - Laranja suave
- **⭐ Secundária**: `#ffca28` - Amarelo quente
- **🌿 Terciária**: `#66bb6a` - Verde suave

### Backgrounds & Textos
| Elemento | Light Mode | Dark Mode |
|----------|------------|-----------|
| **Background Principal** | `#ffffff` | `#121212` |
| **Background Secundário** | `#f8f9fa` | `#1e1e1e` |
| **Background Terciário** | `#e9ecef` | `#2d2d2d` |
| **Texto Principal** | `#212529` | `#ffffff` |
| **Texto Secundário** | `#6c757d` | `#b3b3b3` |

---

## 📱 Estrutura do Site

### 🧭 Header/Navegação
```
[IA] IA FORTE    |    Início • Serviços • Jurídico • Contato    |    🌙
```

**Elementos:**
- **Logo**: Ícone "IA" + texto "IA FORTE"
- **Menu**: Navegação principal responsiva
- **Theme Toggle**: Alternador claro/escuro

---

### 🚀 Hero Section

**Layout**: 2 colunas responsivas (mobile: empilhadas)

#### Coluna Esquerda - Conteúdo Principal
```markdown
# Transforme seu Negócio com Inteligência Artificial

Consultoria especializada em IA para micro e pequenas empresas, 
com foco especial no setor jurídico. Automação inteligente que gera resultados reais.

[Começar Agora] [Saiba Mais]
```

#### Coluna Direita - Features Box
```markdown
✨ Features Destacadas:
• ⚖️ Automação de Peças Jurídicas
• 🤖 Multi-agentes Inteligentes  
• 🔗 Integração com Anthropic
• ⚡ MCP e ADK (Google)
```

---

### 🛠️ Seção Serviços

**Layout**: Grid de 3 cards responsivos

#### Card 1: Curso de Introdução à IA
```markdown
🎯 **Curso de Introdução à IA**

Programa completo de capacitação em IA para negócios, 
com foco prático e aplicação imediata.

✓ Fundamentos de IA para empresas
✓ Casos práticos e aplicações
✓ Certificação inclusa
```

#### Card 2: Consultoria Personalizada
```markdown
🤝 **Consultoria Personalizada**

Análise completa dos processos da sua empresa e 
implementação de soluções de IA sob medida.

✓ Diagnóstico empresarial
✓ Estratégia de implementação
✓ Acompanhamento contínuo
```

#### Card 3: Multi-agentes e MCP
```markdown
🤖 **Multi-agentes e MCP**

Desenvolvimento de sistemas inteligentes com múltiplos 
agentes e integração MCP/ADK.

✓ Agentes especializados
✓ Integração Google ADK
✓ Automação avançada
```

---

### ⚖️ Seção Especialização Jurídica

**Layout**: 2 colunas (conteúdo + métricas)

#### Coluna Esquerda - Descrição
```markdown
## Especialização em Direito

Como profissional do Direito Tributário com experiência em 
consultivo e contencioso, desenvolvemos soluções específicas 
para o setor jurídico.

### 📋 Automação de Peças Jurídicas
Sistema de prompts especializados para criação automática 
de petições, contratos e pareceres.

### 🧠 Integração Anthropic
Tecnologia de ponta com modelos Claude para análise 
jurídica avançada e precisão técnica.

### 📚 Fundamentação Automatizada
Busca e aplicação automática de jurisprudência e 
base normativa relevante.
```

#### Coluna Direita - Métricas
```markdown
## 📊 Resultados Comprovados

┌─────────┬─────────┐
│   90%   │   95%   │
│ Redução │Precisão │
│  Tempo  │Técnica  │
├─────────┼─────────┤
│  24/7   │  100%   │
│Disponib.│Conform. │
└─────────┴─────────┘
```

---

### 📧 Seção Contato

**Layout**: Formulário centralizado + informações

#### Formulário
```markdown
## Entre em Contato
> Pronto para transformar sua empresa com IA? Vamos conversar!

┌─────────────────────────────────┐
│ Nome          │ Empresa         │
├───────────────┴─────────────────┤
│ E-mail                          │
├─────────────────────────────────┤
│ Telefone                        │
├─────────────────────────────────┤
│ Mensagem                        │
│ "Conte-nos sobre seu projeto    │
│  ou necessidade..."             │
└─────────────────────────────────┘

[Enviar Mensagem]
```

#### Informações de Contato
```markdown
📧 contato@iaforte.com.br
🌐 iaforte.com.br
```

---

### 🦶 Footer

**Layout**: 2 colunas responsivas

```markdown
[IA] IA FORTE                    © 2025 IA FORTE
Por Daniel Dias                  Todos os direitos reservados
                                 Consultoria especializada em IA
```

---

## 📐 Layout Responsivo

### 🖥️ Desktop (1200px+)
- Header fixo com navegação horizontal
- Hero em 2 colunas lado a lado
- Serviços em grid 3x1
- Jurídico em 2 colunas
- Footer em 2 colunas

### 📱 Tablet (768px - 1199px)
- Header com menu colapsável
- Hero empilhado (texto → features)
- Serviços em grid 2x2 ou 3x1
- Jurídico empilhado
- Footer empilhado

### 📱 Mobile (< 768px)
- Header compacto com hamburger menu
- Todas as seções empilhadas
- Cards de serviços em coluna única
- Formulário simplificado
- Navigation drawer

---

## 🎯 Componentes Interativos

### Theme Toggle
- **Posição**: Header (direita)
- **Ícones**: 🌙 (light) / ☀️ (dark)
- **Comportamento**: Persiste preferência
- **Transições**: Suaves (0.3s)

### Navegação Mobile
- **Menu Hamburger**: 3 linhas → X animado
- **Slide**: Menu desliza de cima
- **Background**: Overlay com blur

### Animações
- **Scroll**: Elementos aparecem ao entrar na viewport
- **Hover**: Cards elevam com sombra
- **Focus**: Outline colorido para acessibilidade
- **Loading**: Contadores animados nas métricas

---

## 🎨 Guia Visual

### Tipografia
- **Títulos**: Bold, tamanhos hierárquicos
- **Corpo**: Regular, boa legibilidade
- **CTAs**: Semibold, destaque

### Espaçamento
- **Seções**: 80px padding vertical
- **Cards**: 2rem padding interno
- **Grid**: 2rem gap entre elementos

### Shadows & Borders
- **Light Mode**: Sombras sutis
- **Dark Mode**: Bordas e sombras mais fortes
- **Elevation**: 3 níveis de profundidade

---

## ♿ Acessibilidade

### Cores
- **Contraste**: WCAG AA compliant
- **High Contrast**: Suporte nativo
- **Color Blind**: Não depende apenas de cor

### Navegação
- **Keyboard**: Tab order lógico
- **Screen Reader**: ARIA labels
- **Focus**: Indicadores visíveis

### Motion
- **Reduced Motion**: Respeita preferência do sistema
- **Transitions**: Opcionais e suaves

---

*📅 Última atualização: Janeiro 2025*  
*🎨 Design System: IA FORTE v2.0*